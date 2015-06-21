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
        if(!$auth->hasIdentity()){
            //$this->_forward("login", "login");
			return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }else{
            //perfil minico que pode acessa essa pagina
            parent::perfilMinimo(2); //perfil componente da comissao
        }
        
        $idUsuario = $auth->getIdentity()->id_usuario;
        $tblSetorial = new Setorial();
        
        if($auth->getIdentity()->id_perfil == 2){ //se perfil igual a componente
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
            $this->intTamPag = 10;
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
        $apelido        = isset($post->rd_apelido) ? $post->rd_apelido : null;
        $cpf            = isset($post->rd_cpf) ? $post->rd_cpf : null;
        $rg             = isset($post->rd_rg) ? $post->rd_rg : null;
        $dt_nascimento  = isset($post->rd_dte_nascimento) ? $post->rd_dte_nascimento : null;
        $naturalidade   = isset($post->rd_naturalidade) ? $post->rd_naturalidade : null;
        $email          = isset($post->rd_email) ? $post->rd_email : null;
        $endereco       = isset($post->rd_endereco) ? $post->rd_endereco : null;
        $complemento    = isset($post->rd_complemento) ? $post->rd_complemento : null;
        $bairro         = isset($post->rd_bairro) ? $post->rd_bairro : null;
        $cep            = isset($post->rd_cep) ? $post->rd_cep : null;
        $cidade         = isset($post->rd_cidade) ? $post->rd_cidade : null;
        $estado         = isset($post->rd_estado) ? $post->rd_estado : null;
        $formacao       = isset($post->rd_formacao) ? $post->rd_formacao : null;
        $area_atuacao   = isset($post->rd_area_atuacao) ? $post->rd_area_atuacao : null;
        $apresentacao   = isset($post->rd_apresentacao) ? $post->rd_apresentacao : null;
        $segmento       = isset($post->rd_segmento) ? $post->rd_segmento : null;
        $cargo          = isset($post->rd_cargo) ? $post->rd_cargo : null;
        $proposta       = isset($post->rd_proposta) ?  $post->rd_proposta : null;
        $etnia          = isset($post->rd_etnia) ?  $post->rd_etnia : null;
        
        $bln_declaracao_verdadeira  = isset($post->rd_declaracao_verdadeira)   ? $post->rd_declaracao_verdadeira    : null;
        $bln_aprovacao_cadastro     = isset($post->rd_aprovacao_cadastro)      ? $post->rd_aprovacao_cadastro       : null;
        $bln_ciente_planonacional   = isset($post->rd_ciente_planonacional)    ? $post->rd_ciente_planonacional     : null;
        $bln_nao_cargo_comissionado = isset($post->rd_nao_cargo_comissionado)  ? $post->rd_nao_cargo_comissionado   : null;
        $bln_cadastro_candidato     = isset($post->rd_cadastro_candidato)      ? $post->rd_cadastro_candidato       : null;
        
        //anexos
        $file_comprovante_atuacao_setor = isset($post->rd_comprovante_atuacao_setor)   ? $post->rd_comprovante_atuacao_setor: null; 
        $file_identidade                = isset($post->rd_file_identidade)             ? $post->rd_file_identidade          : null; 
        $file_cpf                       = isset($post->rd_file_cpf)                    ? $post->rd_file_cpf                 : null; 
        $file_comprovante_residencia    = isset($post->rd_comprovante_residencia)      ? $post->rd_comprovante_residencia   : null; 
        
        $file_comprovante_funcao_comissionado   = isset($post->rd_comprovante_funcao_comissionado) ? $post->rd_comprovante_funcao_comissionado : null; 
        $file_curriculo                         = isset($post->rd_curriculo)                       ? $post->rd_curriculo    : null; 
        $file_portfolio                         = isset($post->rd_portfolio)                       ? $post->rd_portfolio    : null; 
        $file_carta_apoio                       = isset($post->rd_carta_apoio)                     ? $post->rd_carta_apoio    : null; 
                
        $arrCamposRadio= array( 1 => $nome,
                                2 => $apelido,          
                                3 => $cpf,
                                4 => $rg,
                                5 => $dt_nascimento,
                                6 => $naturalidade,
                                7 => $email,
                                8 => $endereco,
                                9 => $complemento,
                                10 => $bairro,
                                11 => $cep,
                                12 => $cidade,
                                13 => $estado,
                                14 => $formacao,
                                15 => $area_atuacao,
                                16 => $apresentacao,
                                17 => $segmento,
                                18 => $file_comprovante_atuacao_setor,
                                19 => $file_identidade,
                                20 => $file_cpf,
                                21 => $file_comprovante_residencia,
                                22 => $bln_declaracao_verdadeira,
                                23 => $bln_aprovacao_cadastro,
                                24 => $bln_ciente_planonacional,
                                25 => $bln_nao_cargo_comissionado,
                                26 => $cargo,
                                27 => $file_comprovante_funcao_comissionado,
                                28 => $proposta,
                                29 => $file_curriculo,
                                30 => $file_portfolio,
                                31 => $bln_cadastro_candidato,
                                32 => $file_carta_apoio,
                                33 => $etnia
                            );
        
        /* ================================================================================== */
        /* ============= SALVA CAMPOS OBSERVACAO  =========================================== */
        /* ================================================================================== */
        //campos
        $nome           = isset($post->obs_nome) ? $post->obs_nome : null;
        $apelido        = isset($post->obs_apelido) ? $post->obs_apelido : null;
        $cpf            = isset($post->obs_cpf) ? $post->obs_cpf : null;
        $rg             = isset($post->obs_rg) ? $post->obs_rg : null;
        $dt_nascimento  = isset($post->obs_dte_nascimento) ? $post->obs_dte_nascimento : null;
        $naturalidade   = isset($post->obs_naturalidade) ? $post->obs_naturalidade : null;
        $email          = isset($post->obs_email) ? $post->obs_email : null;
        $endereco       = isset($post->obs_endereco) ? $post->obs_endereco : null;
        $complemento    = isset($post->obs_complemento) ? $post->obs_complemento : null;
        $bairro         = isset($post->obs_bairro) ? $post->obs_bairro : null;
        $cep            = isset($post->obs_cep) ? $post->obs_cep : null;
        $cidade         = isset($post->obs_cidade) ? $post->obs_cidade : null;
        $estado         = isset($post->obs_estado) ? $post->obs_estado : null;
        $formacao       = isset($post->obs_formacao) ? $post->obs_formacao : null;
        $area_atuacao   = isset($post->obs_area_atuacao) ? $post->obs_area_atuacao : null;
        $apresentacao   = isset($post->obs_apresentacao) ? $post->obs_apresentacao : null;
        $segmento       = isset($post->obs_segmento) ? $post->obs_segmento : null;
        $cargo          = isset($post->obs_cargo) ? $post->obs_cargo : null;
        $proposta       = isset($post->obs_proposta) ?  $post->obs_proposta : null;
        $etnia          = isset($post->obs_etnia) ?  $post->obs_etnia : null;
        
        $bln_declaracao_verdadeira  = isset($post->obs_declaracao_verdadeira)   ? $post->obs_declaracao_verdadeira    : null;
        $bln_aprovacao_cadastro     = isset($post->obs_aprovacao_cadastro)      ? $post->obs_aprovacao_cadastro       : null;
        $bln_ciente_planonacional   = isset($post->obs_ciente_planonacional)    ? $post->obs_ciente_planonacional     : null;
        $bln_nao_cargo_comissionado = isset($post->obs_nao_cargo_comissionado)  ? $post->obs_nao_cargo_comissionado   : null;
        $bln_cadastro_candidato     = isset($post->obs_cadastro_candidato)      ? $post->obs_cadastro_candidato       : null;
        
        //anexos
        $file_comprovante_atuacao_setor = isset($post->obs_comprovante_atuacao_setor)   ? $post->obs_comprovante_atuacao_setor: null; 
        $file_identidade                = isset($post->obs_file_identidade)             ? $post->obs_file_identidade          : null; 
        $file_cpf                       = isset($post->obs_file_cpf)                    ? $post->obs_file_cpf                 : null; 
        $file_comprovante_residencia    = isset($post->obs_comprovante_residencia)      ? $post->obs_comprovante_residencia   : null; 
        
        $file_comprovante_funcao_comissionado   = isset($post->obs_comprovante_funcao_comissionado) ? $post->obs_comprovante_funcao_comissionado : null; 
        $file_curriculo                         = isset($post->obs_curriculo)                       ? $post->obs_curriculo    : null; 
        $file_portfolio                         = isset($post->obs_portfolio)                       ? $post->obs_portfolio    : null; 
        $file_carta_apoio                       = isset($post->obs_carta_apoio)                     ? $post->obs_carta_apoio  : null;
                
        $arrCamposObs  = array( 1 => $nome,
                                2 => $apelido,          
                                3 => $cpf,
                                4 => $rg,
                                5 => $dt_nascimento,
                                6 => $naturalidade,
                                7 => $email,
                                8 => $endereco,
                                9 => $complemento,
                                10 => $bairro,
                                11 => $cep,
                                12 => $cidade,
                                13 => $estado,
                                14 => $formacao,
                                15 => $area_atuacao,
                                16 => $apresentacao,
                                17 => $segmento,
                                18 => $file_comprovante_atuacao_setor,
                                19 => $file_identidade,
                                20 => $file_cpf,
                                21 => $file_comprovante_residencia,
                                22 => $bln_declaracao_verdadeira,
                                23 => $bln_aprovacao_cadastro,
                                24 => $bln_ciente_planonacional,
                                25 => $bln_nao_cargo_comissionado,
                                26 => $cargo,
                                27 => $file_comprovante_funcao_comissionado,
                                28 => $proposta,
                                29 => $file_curriculo,
                                30 => $file_portfolio,
                                31 => $bln_cadastro_candidato,
                                32 => $file_carta_apoio,
                                33 => $etnia
                            );
        
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
                        $dadosCadXItem = array( "chr_validacao" => $valorCampo);
                        $where = array();
                        $where['id_cadastro = ?'] = $idCadastro;
                        $where['id_item = ?']     = $idCampo;
                        $tblCadastroXItem->alterar($dadosCadXItem, $where);
                    }
                }
                
                $dadosCadXItem = array();
                //GRAVA AS OBSERVACOES - CAMPOS OBSERVACAO
                foreach($arrCamposObs as $idCampo => $valorCampo){

                    if(!empty($valorCampo)){
                        
                        //ATUALIZA CADASTRO
                        $dadosCadXItem = array( "vhr_observacao" => $valorCampo);
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
} // fecha class