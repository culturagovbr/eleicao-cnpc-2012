<?php
/**
 * Login e autenticao
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class AvaliacaoController extends GenericController
{
    /**
        * Metodo principal
        * @access public
        * @param void
        * @return void
        */
    
    public function init()
    {
		$auth  = Zend_Auth::getInstance();
        //if(date('YmdHis') > '20120902235959' && $auth->getIdentity()->id_perfil < 4){ //real
        //if(date('YmdHis') > '20120831202000'){ //teste
                $html = '<center>
                                 <table class="w900" cellpading="0" cellspacing="0" style="background-color: #fff; height: 400px;">
                                 <tr>
                                        <td align="center">
                                                <br>
                                                <img src="'.Zend_Controller_Front::getInstance()->getBaseUrl().'/public/img/logo-cnpc.gif"><br><br>
                                        </td>
                                 </tr>
                                 <tr>
                                        <td align="center">
                                                <h2 style="font-family:Verdana;">Período de avaliação encerrado</h2>
                                        </td>
                                 </tr>
                                 </table>
                                 <center><center>
                                ';
                //echo $html;
                //die;
        //}
        
        if(!$auth->hasIdentity()){
            //$this->_forward("login", "login");
			return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }else{
            //perfil minico que pode acessa essa pagina
            //parent::perfilMinimo(4); //perfil componente da comissao
        }
        
        $idUsuario = $auth->getIdentity()->id_usuario;
        $tblSetorial = new Setorial();
        
        if($auth->getIdentity()->id_perfil == 2 || $auth->getIdentity()->id_perfil == 3){ //se perfil igual a componente
            $tblUsuarioXSetorial = new UsuarioXSetorial();
            $rsUsuarioXSetorial = $tblUsuarioXSetorial->buscar(array('id_usuario=?'=>$idUsuario));

            $arrIdsSetorial = array();
            foreach($rsUsuarioXSetorial as $setorial){
                $arrIdsSetorial[]=$setorial['id_setorial'];
            }
            $rsSetorial = $tblSetorial->buscar(array('id_setorial IN (?)'=>$arrIdsSetorial));
            $this->view->setoriaisCombo = $rsSetorial;
            $this->view->arrSetoriais   = $rsSetorial->toArray();
            $this->view->arrIdSetoriais   = implode(',', $arrIdsSetorial);
        }else{
            $setoriais = $tblSetorial->buscar();
            $this->view->setoriaisCombo = $setoriais;
            $this->view->arrSetoriais   = $setoriais->toArray();
            $this->view->arrIdSetoriais = null;
        }
        
        parent::init();
    }
    public function indexAction()
    {
        
    }
    
    public function formAvaliacaoAction(){
        
        $idCadastro = $this->_request->getParam("id"); // pega o id via get
        
        $auth  = Zend_Auth::getInstance();
        $this->view->idUsuarioAvaliador = $auth->getIdentity()->id_usuario;
        $this->view->idPerfil           = $auth->getIdentity()->id_perfil;
        
        if(!empty($idCadastro)){
            $tblCadastro        = new Cadastro();
            $arrBusca = array();
            $arrBusca['c.id_cadastro = ?'] = $idCadastro;
            $rsCadastro = $tblCadastro->buscaCompleta($arrBusca); 

			$idUsuario = $rsCadastro[0]->id_usuario;
			
            //SETA OS VALORES DOS CAMPOS PARA ENVIAR PARA VIEW
            $this->setarValoresCamposView($rsCadastro);
			if(empty($idUsuario)){
				parent::message("Não foram encontradas informações suficientes para carregar o cadastro deste inscrito.", "/avaliacao", "ERROR");
			}
			$tblUsuarioXSetorial = new UsuarioXSetorial();
            $rsUsuarioXSetorial = $tblUsuarioXSetorial->buscar(array('id_usuario=?'=>$idUsuario))->current();
			$idSetorial = $rsUsuarioXSetorial->id_setorial ;
			$this->view->idSetorialUsuario = $rsUsuarioXSetorial->id_setorial ;
			
			$this->view->segmentos = null;
			if(!empty($idSetorial)){
				$tblSetorialXSegmento = new SetorialXSegmento();
				$rsSetorialXSegmento = $tblSetorialXSegmento->buscaCompleta(array('ss.id_setorial=?'=>$idSetorial));
				$this->view->segmentos = $rsSetorialXSegmento;
			}
            /*$rsStatusAvaliacao = $tblCadastro->buscarStatusAvaliacao(array('c.id_cadastro = ?' => $idCadastro))->current();
			$bln_validacaofinal = $rsStatusAvaliacao->bol_validacaofinal;
			$this->view->bln_validacaofinal = $bln_validacaofinal;*/
        }   
    }
    
    public function listarCadastrosAction() {
        
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $post = Zend_Registry::get('post');
        if($post->intTamPag){
            $this->intTamPag = $post->intTamPag;
        }else{
            $this->intTamPag = 30;
        }

        $pag = 1;
        if (isset($post->pag)) $pag = $post->pag;
        if (isset($post->tamPag)) $this->intTamPag = $post->tamPag;

        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim = $inicio + $this->intTamPag;
        
        $arrBusca=array();
        $arrBusca["bol_cadastroenviado = ?"] = 1;
        if(!empty($_POST['cpf'])){
            $arrBusca["u.vhr_login = ?"] = retiraMascara($_POST['cpf']);
        }
        if(!empty($_POST['nome'])){
            $nome = utf8_decode($_POST['nome']);
            $arrBusca["u.vhr_nome like '%{$nome}%'"] = '?';
        }
        if(!empty($_POST['setorial'])){
            $arrBusca["us.id_setorial = ?"] = $_POST['setorial'];
        }
        if(isset($_POST['idSetoriais']) && !empty($_POST['idSetoriais'])){
            $arrBusca["us.id_setorial IN (?)"] = explode(',',$_POST['idSetoriais']);
        }
        $arrBusca["u.id_perfil IN (?)"] = array(1,2,3);
        //xd($arrBusca);
        $tblCadastro = new Cadastro();
        $total = 0;
        //$total = $tblCadastro->buscaCadastro($arrBusca, array(), null, null, false,true);
        $total = $tblCadastro->buscaCadastro($arrBusca, array(), null, null, true);

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) $fim = $total;

        $ordem = array();
        if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }else{$ordem = array('4 ASC');}
        $rs = $tblCadastro->buscaCadastro($arrBusca, $ordem, $tamanho, $inicio);

        $this->view->registros 	  = $rs;
        $this->view->pag 	  = $pag;
        $this->view->total 	  = $total;
        $this->view->inicio 	  = ($inicio+1);
        $this->view->fim 	  = $fim;
        $this->view->totalPag     = $totalPag;
        $this->view->intTamPag    = $this->intTamPag;
        $this->view->parametrosBusca= $_POST;
    }

    public function salvarAvaliacaoAction(){
        
        $idCadastro = $this->_request->getParam("id"); 
        //xd($_POST);
        $post = Zend_Registry::get('post');
        
        $cadastroEleitor     = isset($post->salvar_eleitor)   ? 1 : null;
        $avaliacaoEleitor    = (isset($post->rd_valida_cadastro_eleitor) && $post->rd_valida_cadastro_eleitor) ? $post->rd_valida_cadastro_eleitor : '0';
        $obsAvaliacaoEleitor = isset($post->obs_valida_cadastro_eleitor) ? $post->obs_valida_cadastro_eleitor : null;
        
        $cadastroCandidato     = isset($post->salvar_candidato) ? 1 : null;
        $avaliacaoCandidato    = (isset($post->rd_valida_cadastro_candidato) && $post->rd_valida_cadastro_candidato) ? $post->rd_valida_cadastro_candidato : '0';
        $obsAvaliacaoCandidato = isset($post->obs_valida_cadastro_candidato) ? $post->obs_valida_cadastro_candidato : null;
        
        $cadastroAvaliacaoFinal  = isset($post->salva_validacao_final) ? 1 : null;
        $avaliacaoFinal          = (isset($post->rd_validacao_final) && $post->rd_validacao_final) ? $post->rd_validacao_final : '0';
        $obsAvaliacaoFinal       = isset($post->obs_validacao_final) ? $post->obs_validacao_final : null;
        
        $idUsuarioAvaliador = isset($post->idUsuarioAvaliador) ? $post->idUsuarioAvaliador : null;
        
        /* ================================================================================== */
        /* ============= SALVA CAMPOS RADIO BUTTON (Sim, Não e Não se aplica) =============== */
        /* ================================================================================== */
        //campos
        $nome           = isset($post->rd_nome) ? $post->rd_nome : null;
        $nome_obs       = isset($post->obs_nome) ? $post->obs_nome : null;
        $apelido        = isset($post->rd_apelido) ? $post->rd_apelido : null;
        $apelido_obs    = isset($post->obs_apelido) ? $post->obs_apelido : null;
        $cpf            = isset($post->rd_cpf) ? $post->rd_cpf : null;
        $cpf_obs            = isset($post->obs_cpf) ? $post->obs_cpf : null;
        $rg             = isset($post->rd_rg) ? $post->rd_rg : null;
        $rg_obs             = isset($post->obs_rg) ? $post->obs_rg : null;
        $dt_nascimento  = isset($post->rd_dte_nascimento) ? $post->rd_dte_nascimento : null;
        $dt_nascimento_obs  = isset($post->obs_dte_nascimento) ? $post->obs_dte_nascimento : null;
        $naturalidade   = isset($post->rd_naturalidade) ? $post->rd_naturalidade : null;
        $naturalidade_obs   = isset($post->obs_naturalidade) ? $post->obs_naturalidade : null;
        $email          = isset($post->rd_email) ? $post->rd_email : null;
        $email_obs          = isset($post->obs_email) ? $post->obs_email : null;
        $endereco       = isset($post->rd_endereco) ? $post->rd_endereco : null;
        $endereco_obs       = isset($post->obs_endereco) ? $post->obs_endereco : null;
        $complemento    = isset($post->rd_complemento) ? $post->rd_complemento : null;
        $complemento_obs    = isset($post->obs_complemento) ? $post->obs_complemento : null;
        $bairro         = isset($post->rd_bairro) ? $post->rd_bairro : null;
        $bairro_obs         = isset($post->obs_bairro) ? $post->obs_bairro : null;
        $cep            = isset($post->rd_cep) ? $post->rd_cep : null;
        $cep_obs            = isset($post->obs_cep) ? $post->obs_cep : null;
        $cidade         = isset($post->rd_cidade) ? $post->rd_cidade : null;
        $cidade_obs         = isset($post->obs_cidade) ? $post->obs_cidade : null;
        $estado         = isset($post->rd_estado) ? $post->rd_estado : null;
        $estado_obs         = isset($post->obs_estado) ? $post->obs_estado : null;
        $formacao       = isset($post->rd_formacao) ? $post->rd_formacao : null;
        $formacao_obs       = isset($post->obs_formacao) ? $post->obs_formacao : null;
        $area_atuacao   = isset($post->rd_area_atuacao) ? $post->rd_area_atuacao : null;
        $area_atuacao_obs   = isset($post->obs_area_atuacao) ? $post->obs_area_atuacao : null;
        $apresentacao   = isset($post->rd_apresentacao) ? $post->rd_apresentacao : null;
        $apresentacao_obs   = isset($post->obs_apresentacao) ? $post->obs_apresentacao : null;
        $segmento       = isset($post->rd_segmento) ? $post->rd_segmento : null;
        $segmento_obs       = isset($post->obs_segmento) ? $post->obs_segmento : null;
        $cargo          = isset($post->rd_cargo) ? $post->rd_cargo : null;
        $cargo_obs          = isset($post->obs_cargo) ? $post->obs_cargo : null;
        $proposta       = isset($post->rd_proposta) ?  $post->rd_proposta : null;
        $proposta_obs       = isset($post->obs_proposta) ?  $post->obs_proposta : null;
        $etnia          = isset($post->rd_etnia) ?  $post->rd_etnia : null;
        $etnia_obs          = isset($post->obs_etnia) ?  $post->obs_etnia : null;
        
        $bln_declaracao_verdadeira  = isset($post->rd_declaracao_verdadeira)   ? $post->rd_declaracao_verdadeira    : null;
        $bln_declaracao_verdadeira_obs  = isset($post->obs_declaracao_verdadeira)   ? $post->obs_declaracao_verdadeira    : null;
        $bln_aprovacao_cadastro     = isset($post->rd_aprovacao_cadastro)      ? $post->rd_aprovacao_cadastro       : null;
        $bln_aprovacao_cadastro_obs     = isset($post->obs_aprovacao_cadastro)      ? $post->obs_aprovacao_cadastro       : null;
        $bln_ciente_planonacional   = isset($post->rd_ciente_planonacional)    ? $post->rd_ciente_planonacional     : null;
        $bln_ciente_planonacional_obs   = isset($post->obs_ciente_planonacional)    ? $post->obs_ciente_planonacional     : null;
        $bln_nao_cargo_comissionado = isset($post->rd_nao_cargo_comissionado)  ? $post->rd_nao_cargo_comissionado   : null;
        $bln_nao_cargo_comissionado_obs = isset($post->obs_nao_cargo_comissionado)  ? $post->obs_nao_cargo_comissionado   : null;
        $bln_cadastro_candidato     = isset($post->rd_cadastro_candidato)      ? $post->rd_cadastro_candidato       : null;
        $bln_cadastro_candidato_obs     = isset($post->obs_cadastro_candidato)      ? $post->obs_cadastro_candidato       : null;
        
        //anexos
        $file_comprovante_atuacao_setor = isset($post->rd_comprovante_atuacao_setor)   ? $post->rd_comprovante_atuacao_setor: null; 
        $file_comprovante_atuacao_setor_obs = isset($post->obs_comprovante_atuacao_setor)   ? $post->obs_comprovante_atuacao_setor: null;
        $file_identidade                = isset($post->rd_file_identidade)             ? $post->rd_file_identidade          : null; 
        $file_identidade_obs                = isset($post->obs_file_identidade)             ? $post->obs_file_identidade          : null;
        $file_cpf                       = isset($post->rd_file_cpf)                    ? $post->rd_file_cpf                 : null; 
        $file_cpf_obs                       = isset($post->obs_file_cpf)                    ? $post->obs_file_cpf                 : null;
        $file_comprovante_residencia    = isset($post->rd_comprovante_residencia)      ? $post->rd_comprovante_residencia   : null; 
        $file_comprovante_residencia_obs    = isset($post->obs_comprovante_residencia)      ? $post->obs_comprovante_residencia   : null;
        
        $file_comprovante_funcao_comissionado   = isset($post->rd_comprovante_funcao_comissionado) ? $post->rd_comprovante_funcao_comissionado : null; 
        $file_comprovante_funcao_comissionado_obs   = isset($post->obs_comprovante_funcao_comissionado) ? $post->obs_comprovante_funcao_comissionado : null;
        $file_curriculo                         = isset($post->rd_curriculo)                       ? $post->rd_curriculo    : null; 
        $file_curriculo_obs                         = isset($post->obs_curriculo)                       ? $post->obs_curriculo    : null;
        $file_portfolio                         = isset($post->rd_portfolio)                       ? $post->rd_portfolio    : null; 
        $file_portfolio_obs                         = isset($post->obs_portfolio)                       ? $post->obs_portfolio    : null;
        $file_carta_apoio                       = isset($post->rd_carta_apoio)                     ? $post->rd_carta_apoio    : null; 
        $file_carta_apoio_obs                       = isset($post->obs_carta_apoio)                     ? $post->obs_carta_apoio    : null;

        $arrCamposRadio= array();
        if($cadastroEleitor){
            $arrCamposRadio[1]['radio'] = $nome;
            $arrCamposRadio[1]['obs'] = $nome_obs;
            $arrCamposRadio[2]['radio'] = $apelido;
            $arrCamposRadio[2]['obs'] = $apelido_obs;
            $arrCamposRadio[3]['radio'] = $cpf;
            $arrCamposRadio[3]['obs'] = $cpf_obs;
            $arrCamposRadio[4]['radio'] = $rg;
            $arrCamposRadio[4]['obs'] = $rg_obs;
            $arrCamposRadio[5]['radio'] = $dt_nascimento;
            $arrCamposRadio[5]['obs'] = $dt_nascimento_obs;
            $arrCamposRadio[6]['radio'] = $naturalidade;
            $arrCamposRadio[6]['obs'] = $naturalidade_obs;
            $arrCamposRadio[7]['radio'] = $email;
            $arrCamposRadio[7]['obs'] = $email_obs;
            $arrCamposRadio[8]['radio'] = $endereco;
            $arrCamposRadio[8]['obs'] = $endereco_obs;
            $arrCamposRadio[9]['radio'] = $complemento;
            $arrCamposRadio[9]['obs'] = $complemento_obs;
            $arrCamposRadio[10]['radio'] = $bairro;
            $arrCamposRadio[10]['obs'] = $bairro_obs;
            $arrCamposRadio[11]['radio'] = $cep;
            $arrCamposRadio[11]['obs'] = $cep_obs;
            $arrCamposRadio[12]['radio'] = $cidade;
            $arrCamposRadio[12]['obs'] = $cidade_obs;
            $arrCamposRadio[13]['radio'] = $estado;
            $arrCamposRadio[13]['obs'] = $estado_obs;
            $arrCamposRadio[14]['radio'] = $formacao;
            $arrCamposRadio[14]['obs'] = $formacao_obs;
            $arrCamposRadio[15]['radio'] = $area_atuacao;
            $arrCamposRadio[15]['obs'] = $area_atuacao_obs;
            $arrCamposRadio[16]['radio'] = $apresentacao;
            $arrCamposRadio[16]['obs'] = $apresentacao_obs;
            $arrCamposRadio[17]['radio'] = $segmento;
            $arrCamposRadio[17]['obs'] = $segmento_obs;
            $arrCamposRadio[18]['radio'] = $file_comprovante_atuacao_setor;
            $arrCamposRadio[18]['obs'] = $file_comprovante_atuacao_setor_obs;
            $arrCamposRadio[19]['radio'] = $file_identidade;
            $arrCamposRadio[19]['obs'] = $file_identidade_obs;
            $arrCamposRadio[20]['radio'] = $file_cpf;
            $arrCamposRadio[20]['obs'] = $file_cpf_obs;
            $arrCamposRadio[21]['radio'] = $file_comprovante_residencia;
            $arrCamposRadio[21]['obs'] = $file_comprovante_residencia_obs;
            $arrCamposRadio[22]['radio'] = $bln_declaracao_verdadeira;
            $arrCamposRadio[22]['obs'] = $bln_declaracao_verdadeira_obs;
            $arrCamposRadio[23]['radio'] = $bln_aprovacao_cadastro;
            $arrCamposRadio[23]['obs'] = $bln_aprovacao_cadastro_obs;
            $arrCamposRadio[24]['radio'] = $bln_ciente_planonacional;
            $arrCamposRadio[24]['obs'] = $bln_ciente_planonacional_obs;
            $arrCamposRadio[25]['radio'] = $bln_nao_cargo_comissionado;
            $arrCamposRadio[25]['obs'] = $bln_nao_cargo_comissionado_obs;
            $arrCamposRadio[26]['radio'] = $cargo;
            $arrCamposRadio[26]['obs'] = $cargo_obs;
            $arrCamposRadio[27]['radio'] = $file_comprovante_funcao_comissionado;
            $arrCamposRadio[27]['obs'] = $file_comprovante_funcao_comissionado_obs;
            $arrCamposRadio[33]['radio'] = $etnia;
            $arrCamposRadio[33]['obs'] = $etnia_obs;
        }
        if($cadastroCandidato){
            $arrCamposRadio[28]['radio'] = $proposta;
            $arrCamposRadio[28]['obs'] = $proposta_obs;
            $arrCamposRadio[29]['radio'] = $file_curriculo;
            $arrCamposRadio[29]['obs'] = $file_curriculo_obs;
            $arrCamposRadio[30]['radio'] = $file_portfolio;
            $arrCamposRadio[30]['obs'] = $file_portfolio_obs;
            $arrCamposRadio[31]['radio'] = $bln_cadastro_candidato;
            $arrCamposRadio[31]['obs'] = $bln_cadastro_candidato_obs;
            $arrCamposRadio[32]['radio'] = $file_carta_apoio;
            $arrCamposRadio[32]['obs'] = $file_carta_apoio_obs;
        }
        
        $tblCadastro      = new Cadastro();
        $tblCadastroXItem = new CadastroXItem();
        
        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        
        if(!empty($idCadastro)){

            try{
                
                $db->beginTransaction();
                
                if($cadastroEleitor){
                    $dadosCadastro["bol_validacaocadastroeleitor"] = $avaliacaoEleitor;
                    $dadosCadastro["vhr_validacaocadastroeleitor"] = $obsAvaliacaoEleitor;
                }
                if($cadastroCandidato){
                    $dadosCadastro["bol_validacaocadastrocandidato"] = $avaliacaoCandidato;
                    $dadosCadastro["vhr_validacaocadastrocandidato"] = $obsAvaliacaoCandidato;
                }
                if($cadastroAvaliacaoFinal){ //avaliacao do presidente
                    $dadosCadastro["bol_validacaofinal"] = $avaliacaoFinal;
                    $dadosCadastro["dte_validacaofinal"] = new Zend_Db_Expr('GETDATE()');
                    $dadosCadastro["vhr_validacaofinal"] = $obsAvaliacaoFinal;
                }
        
                $dadosCadastro["id_usuario_avaliador"] = $idUsuarioAvaliador;
                
                $where = array();
                $where['id_cadastro = ?'] = $idCadastro;
                $tblCadastro->alterar($dadosCadastro, $where);
                
                //GRAVA AS AVALIACOES - CAMPOS RADIO (Sim, Nao, Nao se aplica)
                foreach($arrCamposRadio as $idCampo => $valorCampo){

                    if(!empty($valorCampo)){
                        
                        //ATUALIZA CADASTRO
                        $dadosCadXItem = array( "chr_validacao" => $valorCampo['radio'],
                                                "vhr_observacao" => $valorCampo['obs']);
                        $where = array();
                        $where['id_cadastro = ?'] = $idCadastro;
                        $where['id_item = ?']     = $idCampo;
                        $tblCadastroXItem->alterar($dadosCadXItem, $where);
                    }
                }
                
                $db->commit();
                parent::message("Avaliação registrada com sucesso", "/avaliacao", "CONFIRM");
                
            }catch(Exception $e){
                $db->rollBack();
                parent::message("Erro ao registrar a avalição, tente novamente mais tarde. ".$e->getMessage(), "/avaliacao", "ERROR");
            }
            
        }else{
            
            parent::message("Cadastro não informado", "/avaliacao", "ERROR");
        }
        
    }


    public function alterarStatusAvaliacaoFinalAction()
    {

        $auth  = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }else{
            //perfil minico que pode acessa essa pagina
            parent::perfilMinimo(4); //perfil componente da comissao
        }

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;
    }



    public function listarInscritosAvaliacaoFinalAction() {

        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout

        $post = Zend_Registry::get('post');
        if($post->intTamPag){
            $this->intTamPag = $post->intTamPag;
        }else{
            $this->intTamPag = 30;
        }

        $pag = 1;
        if (isset($post->pag)) $pag = $post->pag;
        if (isset($post->tamPag)) $this->intTamPag = $post->tamPag;

        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim = $inicio + $this->intTamPag;

        $arrBusca = array();
        
        if(!empty($_POST['nome'])){
            $nome = utf8_decode($_POST['nome']);
            $arrBusca["a.vhr_nomeinscrito like '%{$nome}%'"] = '?';
        }
        if(!empty($_POST['tipoInscricao'])){
            $arrBusca["a.int_tipocadastro = ?"] = $_POST['tipoInscricao'];
        }
        if(!empty($_POST['setorial'])){
            $arrBusca["s.id_setorial = ?"] = $_POST['setorial'];
        }
        
        $tbAvalicaoFinal = new AvaliacaoFinalInscrito();
        $total = 0;
        $total = $tbAvalicaoFinal->buscaCompleta($arrBusca, array(), null, null, true);

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) $fim = $total;

        $ordem = array();
        if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }else{$ordem = array('5 ASC');}
        $rs = $tbAvalicaoFinal->buscaCompleta($arrBusca, $ordem, $tamanho, $inicio);

        $this->view->registros 	  = $rs;
        $this->view->pag 	  = $pag;
        $this->view->total 	  = $total;
        $this->view->inicio 	  = ($inicio+1);
        $this->view->fim 	  = $fim;
        $this->view->totalPag     = $totalPag;
        $this->view->intTamPag    = $this->intTamPag;
        $this->view->parametrosBusca= $_POST;
        $this->view->idSetorial   = $idSetorial;
    }

    public function formAlterarStatusAvaliacaoFinalAction(){

        $idRecurso = $this->_request->getParam("id"); // pega o id via get

        $auth  = Zend_Auth::getInstance();
        $this->view->idUsuarioAtualizador = $auth->getIdentity()->id_usuario;

        $idAvaliacaoFinal = $this->_request->getParam("id");
        $this->view->idAvaliacaoFinal = $idAvaliacaoFinal;

        if(!empty($idAvaliacaoFinal)){

            $tblAvaliacaoFinal = new AvaliacaoFinalInscrito();
            $arrBusca = array();
            $arrBusca['a.id_avaliacao_final = ?'] = $idAvaliacaoFinal;
            $rsAvalicao = $tblAvaliacaoFinal->buscaCompleta($arrBusca)->current();
            //xd($rsAvalicao);
            if(!empty($rsAvalicao))
            {
                $this->view->dadosAvaliacao = $rsAvalicao;

            }else{
                parent::message("Nenhum informação encontrada com os parâmetros informados.", "/avaliacao/alterar-status-avaliacao-final", "ERROR");
            }

        }else{
            parent::message("Nenhum informação encontrada com os parâmetros informados.", "/avaliacao/alterar-status-avaliacao-final", "ERROR");
        }
    }


    public function salvarStatusAvaliacaoFinalAction(){

        $post = Zend_Registry::get('post');
        $idUsuarioAtualizador = $post->idUsuarioAtualizador;
        $idAvaliacaoFinal     = $post->idAvaliacaoFinal;
        $int_tipocadastro     = $post->int_tipocadastro;
        $rd_status_eleitor    = $post->rd_status_eleitor;
        $justificativa        = $post->txt_justificativa;

x($_POST);
        try{

            $tblAvaliacaoFinal = new AvaliacaoFinalInscrito();
            $arrBusca = array();
            $arrBusca['id_avaliacao_final = ?'] = $idAvaliacaoFinal;
            $rsAvalicao = $tblAvaliacaoFinal->buscar($arrBusca)->current();

            //busca informações atuais para o log
            $statusEleitorAtual                 = $rsAvalicao->chr_avaliacao_eleitor;
            $statusCandidatoAtual               = $rsAvalicao->chr_avaliacao_candidato;
            $idUsuarioResponsavelAlteracaoAtual = $rsAvalicao->id_usuario_resp_alteracao;
            $justificativaAlteracaoAtual        = $rsAvalicao->vhr_motivo_alteracao;
            $log                                = $rsAvalicao->vhr_log_alteracao;
            $dte                                = $rsAvalicao->dte_alteracao;

            //seta novos valores aos campos
            $rsAvalicao->chr_avaliacao_eleitor      = $rd_status_eleitor;
            $rsAvalicao->id_usuario_resp_alteracao  = $idUsuarioAtualizador;
            $rsAvalicao->vhr_motivo_alteracao  = $justificativa;
            $rsAvalicao->dte_alteracao              = new Zend_Db_Expr('GETDATE()');
            if($int_tipocadastro == 2){
                $rsAvalicao->chr_avaliacao_candidato  = $post->rd_status_candidato;
            }

            $statusEleitorAtual = ($statusEleitorAtual == 1) ? "Validado | " : "Não validado | ";
            $dte = (!empty($dte)) ? date(("d/m/Y"),strtotime($dte))." às ".date(("H:i"),strtotime($dte)) : date("d/m/Y")." às ". date("H:i");
            //registra variavel de log com as informações atuais
            $log  = (!empty($log)) ? $log."\n\n" : $log;
            $logAtual = $log." Informações Em ".$dte." -> Situação do Eleitor: ".$statusEleitorAtual;
            if($int_tipocadastro == 2){
                $statusCandidatoAtual = ($statusCandidatoAtual == 1) ? "Validado | " : "Não validado | ";
                $logAtual .= "Situação do Candidato: ".$statusCandidatoAtual;
            }
            $idUsuarioResponsavelAlteracaoAtual = (!empty($idUsuarioResponsavelAlteracaoAtual)) ? $idUsuarioResponsavelAlteracaoAtual." | " : "Não se aplica | ";
            $justificativaAlteracaoAtual        = (!empty($justificativaAlteracaoAtual)) ? $justificativaAlteracaoAtual : "Não se aplica | ";
            $logAtual .= "Usuário responsavel pela atualizaçao: ".$idUsuarioResponsavelAlteracaoAtual." Justificativa de alteração: ".$justificativaAlteracaoAtual;

            //registra variavel dde log com as novas inforamções
            $logNovo = $logAtual." \n ";
            $rd_status_eleitor = ($rd_status_eleitor == 1) ? "Validado | " : "Não validado | ";
            $logNovo .= "Informações atualizadas Em ".date("d/m/Y")." às ". date("H:i")." -> Situação do Eleitor: ".$rd_status_eleitor;
            if($int_tipocadastro == 2){
                $rd_status_candidato =($post->rd_status_candidato == 1) ? "Validado | " : "Não validado | ";
                $logNovo .= "Situação do Candidato: ".$rd_status_candidato;
            }
            $logNovo .= "Usuário responsavel pela atualizaçao: ".$idUsuarioAtualizador." | Justificativa de alteração: ".$justificativa;
//xd($logNovo);
            $logFinal = $logNovo;
            $rsAvalicao->vhr_log_alteracao        = $logFinal;
//xd($rsAvalicao);
            $rsAvalicao->save();
            
            parent::message("Informações alteradas com sucesso", "/avaliacao/alterar-status-avaliacao-final", "CONFIRM");

        }catch(Exception $e){
xd($e->getMessage());
            parent::message("Erro ao alterar informações, tente novamente mais tarde. ".$e->getMessage(), "/avaliacao/alterar-status-avaliacao-final", "ERROR");
        }
    }
} // fecha class