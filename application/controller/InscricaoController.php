<?php

//require_once "GenericController.php";

class InscricaoController extends GenericController
//class InscricaoController extends Zend_Controller_Action
{

    private $diretorioUpload = null;
    private $caminhoAcessoArquivo = null;
    
    public function init()
    {
        /* Initialize action controller here */
        
        $this->diretorioUpload = getcwd() . "/public/anexos/";
        $this->caminhoAcessoArquivo = Zend_Controller_Front::getInstance()->getBaseUrl() . "/public/anexos/";
        
        parent::init();
        
        //if(date('YmdHis') > '20120826235959'){  //real
		//if(date('YmdHis') > '20120821160700'){  //teste
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
							<h2 style="font-family:Verdana;">Inscri��es encerradas.</h2>
						</td>
					 </tr>
					 </table>
					 <center><center>
					';
			echo $html;
			//x(date('YmdHis'));
			die;
		//}
        
    }

    public function indexAction()
    {
        $this->_forward("form-inscricao");
    }

    public function formInscricaoAction()
    {
        $auth  = Zend_Auth::getInstance();
        //$tblCadastro = new Cadastro();
		//$rsCadastro = $tblCadastro->buscar();
		//xd($rsCadastro->toArray());
        if($auth->hasIdentity() && $auth->getIdentity()->id_perfil != '1'){
            parent::message("O seu perfil n�o lhe permite acessar o formul�rio de cadastro.", "/admin", "ALERT");
        }
        
        $idSetorial = $this->_request->getParam("setorial"); // pega o id do pronac via get
        $idUsuario = ($this->_request->getParam("usu")) ? $this->_request->getParam("usu") : @$auth->getIdentity()->id_usuario; // pega o id do pronac via get
        		
        $tblSetorial = new Setorial();
        if(empty($idSetorial) && empty($idUsuario)){
            //parent::message("� necess�rio informar um setorial para poder realizar o seu cadastro.", "/login", "ALERT");
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
            
        }elseif(!empty($idSetorial)){
            $rsSetorial = $tblSetorial->buscar(array('id_setorial=?'=>$idSetorial))->current();
			if(empty($rsSetorial)){
				parent::message("� necess�rio informar um setorial para poder realizar o seu cadastro.", "/admin", "ALERT");
			}
            $this->view->nomeSetorial = (!empty($rsSetorial)) ? $rsSetorial->vhr_nome : null; 
            $this->view->idSetorial = $idSetorial;
        }
        
        if(!empty($idUsuario) && $idUsuario == $auth->getIdentity()->id_usuario){
            $tblCadastro        = new Cadastro();
            $arrBusca = array();
            $arrBusca['c.id_usuario = ?'] = $idUsuario;
            $rsCadastro = $tblCadastro->buscaCompleta($arrBusca);
         
            //SETA OS VALORES DOS CAMPOS PARA ENVIAR PARA VIEW
            $this->setarValoresCamposView($rsCadastro);
            $this->view->idUsuario = $idUsuario;
            
            $tblUsuarioXSetorial = new UsuarioXSetorial();
            $rsUsuarioXSetorial = $tblUsuarioXSetorial->buscar(array('id_usuario=?'=>$idUsuario))->current();

            if(!empty($rsUsuarioXSetorial)){
                $idSetorial = $rsUsuarioXSetorial->id_setorial ;
                $this->view->idSetorial = $idSetorial;
                $rsSetorial = $tblSetorial->buscar(array('id_setorial=?'=>$rsUsuarioXSetorial->id_setorial))->current();
                $this->view->nomeSetorial = (!empty($rsSetorial)) ? $rsSetorial->vhr_nome : null;
            }/*else{
                if(!empty($idSetorial)){
                    $rsSetorial = $tblSetorial->buscar(array('id_setorial=?'=>$idSetorial))->current();
                    $this->view->nomeSetorial = (!empty($rsSetorial)) ? $rsSetorial->vhr_nome : null; 
                    $this->view->idSetorial = $idSetorial;
                }else{
                    parent::message("� necess�rio informar um setorial para poder realizar o seu cadastro.", "/admin", "ALERT");
                }
            } */ 
        }
        
        $this->view->segmentos = null;
        if(!empty($idSetorial)){
            $tblSetorialXSegmento = new SetorialXSegmento();
            $rsSetorialXSegmento = $tblSetorialXSegmento->buscaCompleta(array('ss.id_setorial=?'=>$idSetorial));
            $this->view->segmentos = $rsSetorialXSegmento;
        }
        
        $dataInicio = date('d-m-Y H:i:s');
        $dataFim = '26-08-2012 23:59:59';
        $diasRestantes = Data::CompararDatas($dataInicio, $dataFim);
        $this->view->diasRestantes = floor($diasRestantes+1);
        
        
        //$this->view->idSetorial = 13; 
        
    }
    
    public function salvarInscricaoAction()
    {
        $post = Zend_Registry::get('post');
              //xd($_FILES);
        //campos
        $idSetorial     = $post->idSetorial;
        $idUsuario      = $post->idUsuario;
        $nome           = $post->vhr_nome;
        $apelido        = $post->vhr_apelido;
        $cpf            = retiraMascara($post->vhr_cpf);
        $senha          = $post->vhr_senha;
        $rg             = $post->vhr_rg;
        $dt_nascimento  = retiraMascara($post->dte_nascimento);
        $naturalidade   = $post->vhr_naturalidade;
        $email          = $post->vhr_email;
        $endereco       = $post->vhr_endereco;
        $complemento    = $post->vhr_complemento;
        $bairro         = $post->vhr_bairro;
        $cep            = retiraMascara($post->vhr_cep);
        $cidade         = $post->vhr_cidade;
        $estado         = $post->vhr_estado;
        $formacao       = $post->vhr_formacao;
        $area_atuacao   = $post->vhr_areadeatuacao;
        $apresentacao   = $post->vhr_apresentacao;
        $segmento       = $post->vhr_segmento;
        $cargo          = $post->vhr_cargo;
        $proposta       = $post->vhr_proposta;
        $etnia          = $post->vhr_etnia;
        
        $bln_declaracao_verdadeira  = isset($post->int_declaracao_verdadeira)   ? 1 : null;
        $bln_aprovacao_cadastro     = isset($post->int_aprovacao_cadastro)      ? 1 : null;
        $bln_ciente_planonacional   = isset($post->int_ciente_planonacional)    ? 1 : null;
        $bln_nao_cargo_comissionado = isset($post->int_nao_cargo_comissionado)  ? 1 : null;
        $bln_cadastro_candidato     = isset($post->int_cadastro_candidato)      ? 1 : null;
        
        $mxResult = array();
        
        $arrCampos = array( 1 => $nome,             
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
                            14 => trim($formacao),
                            15 => trim($area_atuacao),
                            16 => trim($apresentacao),
                            17 => $segmento,
                            //18 => file_comprovante_atuacao_setor,
                            //19 => file_identidade,
                            //20 => file_cpf,
                            //21 => file_comprovante_residencia,
                            22 => $bln_declaracao_verdadeira,
                            23 => $bln_aprovacao_cadastro,
                            24 => $bln_ciente_planonacional,
                            25 => $bln_nao_cargo_comissionado,
                            31 => $bln_cadastro_candidato,
                            33 => $etnia
                        );
        
        //GRAVA CAMPOS SOMENTE SE FOR CARGO COMISSIONADO
        if(!$bln_nao_cargo_comissionado){
            $arrCampos[26] = trim($cargo);
            //$arrCampos[27] = $file_comprovante_funcao_comissionado;
        }
        //GRAVA CAMPOS SOMENTE SE FOR CANDIDATOS
        if($bln_cadastro_candidato){
            $arrCampos[28] = trim($proposta);
            //$arrCampos[29] = $file_curriculo;
            //$arrCampos[30] = $file_portfolio;
        }
        
        $file_comprovante_atuacao_setor         = isset($_FILES['file_comprovante_atuacao_setor'])          ? 'file_comprovante_atuacao_setor' : "";
        $file_identidade                        = isset($_FILES['file_identidade'])                         ? 'file_identidade' : "";
        $file_cpf                               = isset($_FILES['file_cpf'])                                ? 'file_cpf' : "";
        $file_comprovante_residencia            = isset($_FILES['file_comprovante_residencia'])             ? 'file_comprovante_residencia' : "";
        $file_comprovante_funcao_comissionado   = isset($_FILES['file_comprovante_funcao_comissionado'])    ? 'file_comprovante_funcao_comissionado' : "";
        $file_curriculo                         = isset($_FILES['file_curriculo'])                          ? 'file_curriculo' : "";
        $file_portifolio                        = isset($_FILES['file_portfolio'])                          ? 'file_portfolio' : "";
        $file_carta_apoio                       = isset($_FILES['file_carta_apoio'])                        ? 'file_carta_apoio' : "";
            
        //anexos
        if($_FILES){
            
            $diretorio = $this->diretorioUpload.$cpf;
            $this->view->diretorioArquivo = $diretorio;
            
            $caminhoArquivo = $this->caminhoAcessoArquivo.$cpf."/";
            $this->view->caminhoAcessoArquivo = $caminhoArquivo;
            
            $mxResult = $this->upload($_FILES,$diretorio);
            
            if(!isset($mxResult['error_geral'])){
                
                foreach($mxResult as $valor){
                    
                    if($valor['campo'] == $file_comprovante_atuacao_setor && $valor['error'] == 'false'){
                        $arrCampos[18] = $caminhoArquivo.$valor['arquivo'];
                    }
                    if($valor['campo'] == $file_identidade && $valor['error'] == 'false'){
                        $arrCampos[19] = $caminhoArquivo.$valor['arquivo'];
                    }
                    if($valor['campo'] == $file_cpf && $valor['error'] == 'false'){
                        $arrCampos[20] = $caminhoArquivo.$valor['arquivo'];
                    }
                    if($valor['campo'] == $file_comprovante_residencia && $valor['error'] == 'false'){
                        $arrCampos[21] = $caminhoArquivo.$valor['arquivo'];
                    }
                    if($valor['campo'] == $file_comprovante_funcao_comissionado && $valor['error'] == 'false'){
                        $arrCampos[27] = $caminhoArquivo.$valor['arquivo'];
                    }
                    if($valor['campo'] == $file_curriculo && $valor['error'] == 'false'){
                        $arrCampos[29] = $caminhoArquivo.$valor['arquivo'];
                    }
                    if($valor['campo'] == $file_portifolio && $valor['error'] == 'false'){
                        $arrCampos[30] = $caminhoArquivo.$valor['arquivo'];
                    }
                    if($valor['campo'] == $file_carta_apoio && $valor['error'] == 'false'){
                        $arrCampos[32] = $caminhoArquivo.$valor['arquivo'];
                    }
                }
            }else{
                parent::message($mxResult['error_msg'], "/inscricao/form-inscricao/setorial/".$idSetorial, "ERROR");
                die;
            }
        }
        
        /* ===========================================*/
        /* ==== Inicio - Definicao de variaveis ======*/
        
        if($bln_cadastro_candidato){
            $int_tipocadastro = 2; //candidato
        }else{
            $int_tipocadastro = 1; //eleitor
        }
        
        $bln_enviar = ($post->bln_enviar == '1') ? $post->bln_enviar : null;
        
        /* ==== Fim Inicio - Definicao de variaveis ====*/
        /* ===========================================*/
        
        $tblUsuario         = new Usuario();
        $tblCadastro        = new Cadastro();
        $tblCadastroXItem   = new CadastroXItem();
        $tblUsuarioXSetorial = new UsuarioXSetorial();
        
        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        
        try{

            $db->beginTransaction();
            /* ==================================================*/
            /* =============  GRAVA USUARIO  ====================*/
            /* ==================================================*/
                
                if(empty($idUsuario)){ 
        
                    //VERIFICA SE CPF JA ESTA CADASTRO
                    $rsUsuario = $tblUsuario->buscar(array('vhr_login = ?' => $cpf))->current(); 
					
                    if(!empty($rsUsuario)){
					
						//$this->_helper->viewRenderer->setNoRender(true);
						//$this->_helper->flashMessenger->addMessage("J� existe um cadastro realizado com este CPF.");
						//$this->_helper->flashMessengerType->addMessage("ERROR");
						//$this->_redirect("inscricao/form-inscricao");
                        parent::message("J� existe um cadastro realizado com este CPF.", "/inscricao/form-inscricao/setorial/".$idSetorial, "ERROR");
                        die;
                    }
                    
                    $PasswordWP = new PasswordHash(8, false);
                    
                    //novo usuario
                    $dadosUsuario = array( 
                                    "id_perfil" => 1, //perfil de inscricao
                                    "vhr_login" => $cpf,
                                    "vhr_senha" => $PasswordWP->HashPassword($senha),
                                    "vhr_nome"  => $nome,
                                    "vhr_email" => $email
                                    );
                    $idUsuario = $tblUsuario->insert($dadosUsuario);
                    
                    $rsUsuario = $tblUsuario->buscar(array('id_usuario = ?'=>$idUsuario))->current();
                    
                    //relaciona usuario com setorial
                    $dadosUsuarioSetorial = array("id_setorial" => $idSetorial, 
                                                  "id_usuario"  => $idUsuario);
                    $tblUsuarioXSetorial->insert($dadosUsuarioSetorial);
                    
                    //AUTENTICA USUARIO
                    $tblUsuario->login($rsUsuario->vhr_login, $rsUsuario->vhr_senha);
                    //$auth  = Zend_Auth::getInstance();
                    //xd($auth->getIdentity());

                }else{ 
        
                    //usuario existente
                    $rsUsuario = $tblUsuario->buscar(array('id_usuario = ?'=>$idUsuario))->current();
                    $idUsuario = $rsUsuario->id_usuario;
                    $dadosUsuario = array();
                    $dadosUsuario['vhr_nome'] = $nome;
                    $dadosUsuario['vhr_email'] = $email;
                    $where = array();
                    $where['id_usuario = ?'] = $idUsuario;
                    $tblUsuario->alterar($dadosUsuario, $where);
                    
                    
                    //relaciona usuario com setorial
                    $where = array();
                    $where['id_usuario = ?']  = $idUsuario;
                    $where['id_setorial = ?'] = $idSetorial;
                    $tblUsuarioXSetorial->delete($where); //apaga a relacao para criar de novo
                    
                    $dadosUsuarioSetorial = array("id_setorial" => $idSetorial, 
                                                  "id_usuario"  => $idUsuario);
                    $tblUsuarioXSetorial->insert($dadosUsuarioSetorial);
                }

            /* ==================================================*/
            /* =============  GRAVA CADASTRO  ===================*/
            /* ==================================================*/
                if(!empty($idUsuario)){
                  
                    //VERIFICA SE O CADASTRO PARA ESTE USUARIO JA EXISTE
                    $rsCadastro = $tblCadastro->buscar(array('id_usuario = ?' => $idUsuario))->current();

                    if(empty($rsCadastro)){ 
                        
                        //cadastro novo
                        $dadosCadastro = array( "id_usuario"            => $idUsuario,
                                                //"id_setorial"           => $nome,
                                                "int_tipocadastro"      => $int_tipocadastro,
                                                "dte_cadastro"          => new Zend_Db_Expr('GETDATE()'),
                                                "bol_cadastroenviado"   => $bln_enviar
                                                );  
                        if($bln_enviar == '1'){
                            $dadosCadastro['dte_enviocadastro'] = new Zend_Db_Expr('GETDATE()');
                        }
                        
                        $idCadastro = $tblCadastro->insert($dadosCadastro);
                        
                    }else{
                        //cadastro existente
                        $idCadastro = $rsCadastro->id_cadastro;
                        
                        $dadosCadastro = array( //"id_setorial"           => $nome,
                                                "int_tipocadastro"      => $int_tipocadastro,
                                                "dte_atualizacao"       => new Zend_Db_Expr('GETDATE()'),
                                                "bol_cadastroenviado"   => $bln_enviar
                                                );  
                        if($bln_enviar == '1'){
                            $dadosCadastro['dte_enviocadastro'] = new Zend_Db_Expr('GETDATE()');
                        }
                        
                        //ATUALIZA CADASTRO
                        $where = array();
                        $where['id_cadastro = ?'] = $idCadastro;
                        $tblCadastro->alterar($dadosCadastro, $where);
                        
                        //APAGA AS RESPOSTAS PARA CADASTRALAS NOVAMENTE
                        //se o cadastro existe apaga todas as respostas para cadastra-las novamente
                        $where = array();
                        $where['id_cadastro = ?'] = $idCadastro;
                        $where['id_item NOT IN (?)'] = array(18,19,20,21,27,29,30,32); //campos de anexos
                        $tblCadastroXItem->delete($where);
                    }
                }

            /* ==================================================*/
            /* =============  GRAVA CADASTRO X ITEM  ============*/
            /* ==================================================*/
                if(!empty($idUsuario) && !empty($idCadastro)){
                    
                    foreach($arrCampos as $idCampo => $valorCampo){

                        //if(!empty($valorCampo)){
                            $dadosCadXItem = array( "id_cadastro" => $idCadastro,
                                                    "id_item"     => $idCampo,
                                                    "vhr_valor"   => $valorCampo
                                                );
                            $tblCadastroXItem->insert($dadosCadXItem);
                            $dadosCadXItem = array();
                        //}
                    }
                }
                
            $db->commit();
            $msg = ($bln_enviar == '1') ? "Cadastro salvo e enviado com sucesso!" : "Cadastro salvo com sucesso!" ;
            
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->flashMessenger->addMessage($msg);
            $this->_helper->flashMessengerType->addMessage("CONFIRM");
            $this->_redirect("inscricao/form-inscricao/usu/{$idUsuario}");
            //parent::message($msg, "inscricao/form-inscricao","CONFIRM");
            
        } catch (Zend_Exception $e){
            
            $db->rollBack();
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->flashMessenger->addMessage("Desculpe, ocorreu um erro ao salvar o cadastro. Tente novamente mais tarde. ".$e->getMessage());
            $this->_helper->flashMessengerType->addMessage("ERROR");
            $this->_redirect("inscricao/form-inscricao/");
            //parent::message("Desculpe, ocorreu um erro ao salvar o cadastro. Tente novamente mais tarde. ".$e->getMessage(), "inscricao/form-inscricao","ERROR");
	}
    }
    
    public function upload($_FILES,$diretorio){
        
        //anexos
        /*if($_FILES){
            $file_comprovante_atuacao_setor          = $_FILES['file_comprovante_atuacao_setor'];
            $file_identidade            = $_FILES['file_identidade'];
            $file_cpf                   = $_FILES['file_cpf'];
            $file_comprovante_residencia = $_FILES['file_comprovante_residencia'];
            $file_comprovante_funcao_comissionado     = $_FILES['file_comprovante_funcao_comissionado'];
            $file_curriculo             = $_FILES['file_curriculo'];
            $file_portifolio            = $_FILES['file_portifolio'];
        }*/
        $arrRetorno = array();
        $ct = 1;
        foreach($_FILES as $campo => $arquivo)
        {    
            $arquivoNome     = $arquivo['name'];     // nome
            $arquivoTemp     = $arquivo['tmp_name']; // nome tempor�rio
            $arquivoTipo     = $arquivo['type'];     // tipo
            $arquivoTamanho  = $arquivo['size'];     // tamanho
            $nome_arquivo    = substr($campo, 5);    //retira o prefixo file
            //x($arquivoTipo);
            //x($arquivo);
            
            //$diretorio = Constantes::ctePathArquivosExtratosContrato;
            $mxdResult = @$this->verificaExistenciaDiretorio($diretorio);	
            
            if($mxdResult['erro']=="false" )
            {
                if(!empty($arquivoTemp))
                {
                    //VALIDA TAMANHO ARQUIVO
                    /*if($arquivoTamanho > 1024)
                    {
                        $mensagem = "O arquivo deve ser menor que 10 MB<br />";
                        $script = "window.parent.jqAjaxLinkSemLoading('".$this->view->baseUrl()."/upload/listar-arquivo-marca$this->cod', '', 'listaDeArquivos');\n";

                        $this->montaTela("upload/mensagem.phtml",
                            array("mensagem"=>$mensagem,
                                    "tipoMensagem"=>"ERROR",
                                    "script"=>$script)
                        );
                        return;
                    }

                    $extensao_arquivo = explode(".",$arquivoNome);
                    $extensao_arquivo = $extensao_arquivo[count($extensao_arquivo)-1];
                    */


                    $arquivoTipo = explode("/",$arquivoTipo);
                    $arquivoTipo = $arquivoTipo[count($arquivoTipo)-1];
                    $arrTipo = array('pdf','PDF');
                    
                    //VALIDA EXTENSAO ARQUIVO
                    if(!in_array($arquivoTipo,$arrTipo))
                    {
                        $arrRetorno['error_geral'] = "true";
                        $arrRetorno['error_msg'] = "S� � permitido o envio de arquivos do tipo .pdf.";
                        $arrRetorno[$ct]['campo'] = $campo;
                        $arrRetorno[$ct]['error'] = 'true';
                        $arrRetorno[$ct]['arquivo'] = $nome_arquivo;
                        return $arrRetorno;
                    }

                    $extensao_arquivo = explode(".",$arquivoNome);
                    $extensao_arquivo = $extensao_arquivo[count($extensao_arquivo)-1];
                    
                    $nome_arquivo = preparaNomeArquivo($nome_arquivo).".".strtolower($extensao_arquivo);

                    $arquivo = $diretorio . "/" . $nome_arquivo;
            
                    //REALIZA UPLOAD DOCUMENTO
                    $resultUpload = @move_uploaded_file($arquivoTemp, $arquivo);
                    if($resultUpload)
                    {
                        $arrRetorno[$ct]['campo'] = $campo;
                        $arrRetorno[$ct]['error'] = 'false';
                        $arrRetorno[$ct]['arquivo'] = $nome_arquivo;
                    }else{
                        $arrRetorno[$ct]['campo'] = $campo;
                        $arrRetorno[$ct]['error'] = 'true';
                        $arrRetorno[$ct]['arquivo'] = $nome_arquivo;
                    };
                }
            }else
            {
                $arrRetorno['error_geral'] = 'true';
                //$erro = "Erro ao criar o diret&oacute;rio de armazenamento do arquivo";
            }
            $ct++;
        }
        
        return $arrRetorno;
    }
    
    public function verificaExistenciaDiretorio($dirCompleto)
    {
        //$dirCompleto = null;
        $erro = "";

        //VERIFICA SE PASTA EXISTE, SENAO CRIA
        if(!@is_dir($dirCompleto)){

            if(!@mkdir($dirCompleto)){
                $erro = "&raquo; N&atilde;o foi poss&iacute;vel encontrar ou criar o diret&oacute;rio <u>".$dirCompleto."</u>.<br>";
            }else{
                @chmod($dirCompleto, 0777);
            }
        }

        if(!empty($erro)){
                $erro .= "&nbsp; &nbsp;O arquivo n&atilde;o anexado.<br><br>";
                $arrResultado['descricao_erro'] = "<font color='red'>".$erro."</font>";
                $arrResultado['erro'] = "true"; 
                return $arrResultado;
        }else{
                $arrResultado['diretorio_completo'] = $dirCompleto;
                $arrResultado['erro'] = "false"; 
                return $arrResultado;	
        }
    }
    public function apagarAnexoAction()
    {
        $idCadastroXItem = $this->_request->getParam("idCadastroXItem"); 
        $nomeArquivo     = $this->_request->getParam("nomeArquivo"); 
        
        try{

            $tblCadastro = new Cadastro();
            $arrBusca = array();
            $arrBusca['ci.id_cadastro_item = ?'] = $idCadastroXItem;
            $rsCadastro = $tblCadastro->buscaCompleta($arrBusca)->current();
            
            $arquivo = $this->diretorioUpload.$rsCadastro->vhr_login."/".$nomeArquivo;
            if(file_exists($arquivo)){
                unlink($arquivo);
            }
            $tblCadastroXItem = new CadastroXItem();
            $rsCadastroXItem = $tblCadastroXItem->buscar(array('id_cadastro_item  = ?' => $idCadastroXItem))->current();
        
            $where = array();
            $where = array('id_cadastro_item = ?' => $idCadastroXItem);
            $tblCadastroXItem->delete($where);
            
            $arrRetorno = array('error' => false,'msg' => 'Arquivo apagado com sucesso');
            echo json_encode($arrRetorno);die;
            
        }catch(Exception $e){
            $arrRetorno = array('error' => true,'msg' => 'Erro ao apagar arquivo');
            echo json_encode($arrRetorno);die;
        }
    }

        
}

