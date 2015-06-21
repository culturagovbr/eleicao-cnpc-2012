<?php
/**
 * Recurso
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class RecursoController extends GenericController
{
    
    private $diretorioUpload = null;
    private $caminhoAcessoArquivo = null;
    
    private $hostSMTP       = null;
    private $port           = null;
    private $auth           = null;
    private $username       = null;
    private $password       = null;
    private $ssl            = null;
    private $config         = array();
    
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
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }
        parent::init(); // chama o init() do pai GenericControllerNew
        
        $this->diretorioUpload = getcwd() . "/public/anexos/";
        $this->caminhoAcessoArquivo = Zend_Controller_Front::getInstance()->getBaseUrl() . "/public/anexos/";

        $auth  = Zend_Auth::getInstance();
        $this->view->login_usuario = $auth->getIdentity()->vhr_login;
        if($auth->getIdentity()->id_perfil <=1){ //real
        //if(date('YmdHis') < '20120904180400'){ //teste
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
                                    <h2 style="font-family:Verdana;">Prazo para solicitação de Recurso finalizado.</h2>
                            </td>
                     </tr>
                     </table>
                     <center><center>
                    ';
            echo $html;
            die;
        }
        
        //$this->hostSMTP = "smtp.gmail.com";
        $this->hostSMTP = "smtp.cultura.gov.br";
        //$this->port     = "465";
        $this->port     = "25";
        //$this->auth     = "login";
        //$this->username = "xti.emails@gmail.com";
        //$this->password = "XTI_Admin";
        //$this->ssl      = "ssl";
        
        $this->config = array (
                                //'auth'     => $this->auth,
                                //'username' => $this->username,
                                //'password' => $this->password,
                                //'ssl'      => $this->ssl,
                                'port'     => $this->port
                              );
    }
    
    public function indexAction()
    {
        $this->_forward("form-recurso");         
    }
    
    public function formRecursoAction()
    {
        $auth  = Zend_Auth::getInstance();
        if($auth->hasIdentity() && $auth->getIdentity()->id_perfil != '1'){
            parent::message("O seu perfil não lhe permite acessar o formulário de cadastro de Recurso.", "/admin", "ALERT");
        }
        
        $visualizacaoAjax = $this->_request->getParam("ajax"); // pega o id via get
        if($visualizacaoAjax){
            header("Content-Type: text/html; charset=ISO-8859-1");
            $this->_helper->layout->disableLayout();// Desabilita o Zend Layout
            $this->view->visualizacaoAjax = "true";
            //$this->renderScript($view);
        }
        
        $idUsuario = @$auth->getIdentity()->id_usuario; // pega o id do inscrito logado
        
        if(!empty($idUsuario)){
            
            $tblCadastro = new Cadastro();
            $arrBusca = array();
            $arrBusca['c.id_usuario = ?'] = $idUsuario;
            $rsCadastro = $tblCadastro->buscaCompleta($arrBusca); 
            $idCadastro = (@isset($rsCadastro[0]->id_cadastro)) ? @$rsCadastro[0]->id_cadastro : null; // pega o id cadastro
        
            if(!empty($idCadastro)){
                
                //SETA OS VALORES DOS CAMPOS PARA ENVIAR PARA VIEW
                $this->setarValoresCamposView($rsCadastro);
                $this->view->idCadastro = $idCadastro;
                
                $tbRecurso = new Recurso();
                $rsRecurso = $tbRecurso->buscar(array("id_cadastro = ?" => $idCadastro))->current();
                $this->view->dadosRecurso = $rsRecurso;
                
                if(!empty($rsRecurso)){
                    $tbAnexoRecurso = new AnexoRecurso();
                    $rsAnexoRecurso = $tbAnexoRecurso->buscar(array("id_recurso = ?" => $rsRecurso->id_recurso));                    
                    $this->setarValoresCamposViewRecurso($rsAnexoRecurso,$rsCadastro);
                }
            }else{
                parent::message("Cadastro não encontrado.", "/admin", "ALERT");
            }
        }else{
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }
    }
    
    
    public function formVisualizacaoAvaliacaoAction()
    {
        header("Content-Type: text/html; charset=ISO-8859-1");
        $this->_helper->layout->disableLayout();// Desabilita o Zend Layout
        
        
        $idCadastro = $this->_request->getParam("idCadastro"); // pega o id via get
        
        if(!empty($idCadastro)){
            $tblCadastro        = new Cadastro();
            $arrBusca = array();
            $arrBusca['c.id_cadastro = ?'] = $idCadastro;
            $rsCadastro = $tblCadastro->buscaCompleta($arrBusca); 
			
            //SETA OS VALORES DOS CAMPOS PARA ENVIAR PARA VIEW
            $this->setarValoresCamposView($rsCadastro);
        } 
    }
    
    public function salvarRecursoAction()
    {
        $auth  = Zend_Auth::getInstance();
        $post = Zend_Registry::get("post");
        
        $idCadastro           = $post->idCadastro;
        $textoRecursoPadrao   = $post->txt_recurso_padrao;
        $textoRecursoDigitado = $post->txt_recurso;
        $arrNomeArquivo       = $post->nomeArquivo;

        $textoRecurso = $textoRecursoPadrao."\n\n".$textoRecursoDigitado;
        
        //DEFINE DIRETORIO DE UPLOAD DOS ARQUIVOS
        $cpf = @$auth->getIdentity()->vhr_login; // pega o cpf do inscrito logado
        //$diretorio = $this->diretorioUpload.$cpf."/recurso";
		$diretorio = $this->diretorioUpload.$cpf;
        $this->view->diretorioArquivo = $diretorio;

        $caminhoArquivo = $this->caminhoAcessoArquivo.$cpf."/recurso";
        $this->view->caminhoAcessoArquivo = $caminhoArquivo;
        
        //VERIFICA SE FOI INFORMADO O ID DO CADASTRO
        if(empty($idCadastro)){
            parent::message("Cadastro não encontrado.", "recurso/form-recurso/", "ERROR");
        }
        //VERIFICA SE EXISTE RECURSO PARA ESTE CADASTRO
        $tbRecurso = new Recurso();
        $rsRecurso = $tbRecurso->buscar(array("id_cadastro = ?" => $idCadastro))->current();
        if(!empty($rsRecurso)){
            //parent::message("Já existe um Recurso cadastrado para esta Inscrição.", "recurso/form-recurso/", "ERROR");
        }

        //REALIZA UPLOAD DOS ARQUIVOS
        $arrRetorno = array();
        
        $ctArqEnviados = 0;
        $numArquivos = count($_FILES['arquivo']['tmp_name']);
        
        //foreach($_FILES as $chave => $arquivo)
        for($i=0; $i < $numArquivos; $i++)
        {    
            
            $arquivoNome     = $_FILES['arquivo']['name'][$i];     // nome
            $arquivoTemp     = $_FILES['arquivo']['tmp_name'][$i]; // nome temporário
            $arquivoTipo     = $_FILES['arquivo']['type'][$i];     // tipo
            $arquivoTamanho  = $_FILES['arquivo']['size'][$i];     // tamanho
            
            $codNomeArquivo = (int)$i+1;
            $nome_arquivo    = (!empty($arrNomeArquivo[$i])) ? $arrNomeArquivo[$i] : 'arquivo_'.$codNomeArquivo;    //retira o prefixo file
            $nome_arquivo_original    = (!empty($arrNomeArquivo[$i])) ? $arrNomeArquivo[$i] : 'Arquivo_'.$codNomeArquivo;    //retira o prefixo file
            
            //$diretorio = Constantes::ctePathArquivosExtratosContrato;
            $mxdResult = @$this->verificaExistenciaDiretorio($diretorio);	
            
            if($mxdResult['erro']=="false" )
            {
                if(!empty($arquivoTemp))
                {
                    $arquivoTipo = explode("/",$arquivoTipo);
                    $arquivoTipo = $arquivoTipo[count($arquivoTipo)-1];
                    $arrTipo = array('pdf','PDF');
                    
                    //VALIDA EXTENSAO ARQUIVO
                    if(!in_array($arquivoTipo,$arrTipo))
                    {
						parent::message("Só é permitido o envio de arquivos do tipo PDF.", "recurso/form-recurso/", "ERROR");
						die;
                    }
                    
                    $nome_arquivo = preparaNomeArquivo($nome_arquivo).".".strtolower($arquivoTipo);

                    $arquivo = $diretorio . "/recurso/" . $nome_arquivo;
            
                    //REALIZA UPLOAD DOCUMENTO
                    $resultUpload = @move_uploaded_file($arquivoTemp, $arquivo);
                    if($resultUpload)
                    {
                        $arrArquivosEnviados[$ctArqEnviados]['nome']    = $nome_arquivo_original;
                        $arrArquivosEnviados[$ctArqEnviados]['caminho'] = $caminhoArquivo."/".$nome_arquivo;
                        $ctArqEnviados++;
                    }else{
                        parent::message("Desculpe, não foi possível subir o arquivo ".$arquivoNome.". Verifique o tipo e o tamanho do mesmo ou tente novamente mais tarde.", "recurso/form-recurso/", "ERROR");
                        die;
                    };
                }
            }
            else
            {
                parent::message("Desculpe, ocorreu um erro ao subir o(s) arquivo(s) enviado(s). Verifique o tipo e o tamanho do(s) mesmo(s) ou tente novamente mais tarde.", "recurso/form-recurso/", "ERROR");
                die;
            }
        }
        
        $tbRecurso = new Recurso();
        $tbAnexoRecurso = new AnexoRecurso();
        
        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        
        try{

            $db->beginTransaction();
            /* ==================================================*/
            /* =============  GRAVA RECURSO  ====================*/
            /* ==================================================*/
            
            //cadastro novo recurso
            $dadosRecurso  = array( "id_cadastro"           => $idCadastro,
                                    "vhr_recurso"           => $textoRecurso,
                                    "dte_recurso"           => new Zend_Db_Expr('GETDATE()')
                                    );  
            
            $idRecurso = $tbRecurso->insert($dadosRecurso);
            
            /* ==================================================*/
            /* =============  GRAVA ANEXOS RECURSO  =============*/
            /* ==================================================*/
            //x($arrArquivosEnviados);
            if(count($arrArquivosEnviados) > 0)
            {
                foreach ($arrArquivosEnviados as $arquivo)
                {
                    $dadosAnexoRecurso  = array( "id_recurso"         => $idRecurso,
                                                 "vhr_nome_anexo"     => $arquivo['nome'],
                                                 "vhr_caminho_anexo"  => $arquivo['caminho']
                                                );  
                    //x($dadosAnexoRecurso);
                    $tbAnexoRecurso->insert($dadosAnexoRecurso);
                }    
            }
            $db->commit();
            
            $mailTransport = new Zend_Mail_Transport_Smtp($this->hostSMTP, $this->config);
        
            /* ================================================*/
            /* ============ E-MAIL PARA O INSCRITO ============*/
            /* ================================================*/
        
            $nome  = @$auth->getIdentity()->vhr_nome;
            $email = @$auth->getIdentity()->vhr_email;
            //$email = "marcosrr@gmail.com";
        
            $nome_remetente  = "Comissão CNPC";
            $email_remetente = "inscricoescnpc@cultura.gov.br";
            $assunto         = "Confirmação de solicitação de Recurso - CNPC";
            $mensagem        = "Prezado Sr(a). ".$nome." <br /><br />";
            $mensagem        .= "Confirmamos o recebimento do Recurso enviado pelo Sr(a). no sítio do Processo Eleitoral do CNPC em ".date("d/m/Y H:i").".<br /><br />";
            $mensagem        .= "Atenciosamente,<br />Comissão CNPC";
            
            $mail = new Zend_Mail();
            $mail->setFrom($email_remetente, $nome_remetente); // Quem esta enviando
            $mail->addTo($email, $nome);  //para quem sera enviado
            $mail->setBodyHtml($mensagem); //texto sem formatacao
            $mail->setSubject($assunto." [".$nome."]"); //assunto
            $mail->send($mailTransport); //envia email
            unset($mail);
            /* ================================================*/
            /* ============ E-MAIL PARA a COMISSAO ============*/
            /* ================================================*/
        
            $email = "cnpccomunicacao@cultura.gov.br";
            //$email = "marcosrr@gmail.com";
        
            $nome_remetente  = "Sítio Processo Eleitoral CNPC";
            $email_remetente = "inscricoescnpc@cultura.gov.br";
            $assunto         = "Formulário de Recurso enviado pelo Sr(a). ".$nome;
            $mensagem        = "O(A) Sr(a). ".$nome." <br /><br />";
            $mensagem        .= "Cadastrou seu Recurso por meio do sítio do Processo Eleitoral do CNPC em ".date("d/m/Y H:i").".<br /><br />";
            $mensagem        .= "Mensagem enviada automaticamente pelo Sistema de Eleitorado do CNPC";
            
            $mail = new Zend_Mail();
            $mail->setFrom($email_remetente, $nome_remetente); // Quem esta enviando
            $mail->addTo($email, "Comissão do Eleitorado CNPC");  //para quem sera enviado
            //$mail->addCc("fabiana.lima@cultura.gob.br", "Comissão do Eleitorado CNPC");  //para quem sera enviado
            $mail->addCc("marcosrr@gmail.com", "Comissão do Eleitorado CNPC");  //para quem sera enviado
            $mail->setBodyHtml($mensagem); //texto sem formatacao
            $mail->setSubject($assunto); //assunto
            $mail->send($mailTransport); //envia email
            unset($mail);
            
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->flashMessenger->addMessage("O seu Recurso foi cadastrado com sucesso.");
            $this->_helper->flashMessengerType->addMessage("CONFIRM");
            $this->_redirect("recurso/form-recurso/");
            //parent::message($msg, "inscricao/form-inscricao","CONFIRM");
            
        } catch (Zend_Exception $e){
            
            $db->rollBack();
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->flashMessenger->addMessage("Desculpe, ocorreu um erro ao salvar o seu recurso. Tente novamente mais tarde. <br />".$e->getMessage());
            $this->_helper->flashMessengerType->addMessage("ERROR");
            $this->_redirect("recurso/form-recurso/");
            //parent::message("Desculpe, ocorreu um erro ao salvar o cadastro. Tente novamente mais tarde. ".$e->getMessage(), "inscricao/form-inscricao","ERROR");
	}
    }
    
    public function verificaExistenciaDiretorio($dirCompleto)
    {
        //$dirCompleto = null;
        $erro = "";

		//VERIFICA SE PASTA DO CPF EXISTE, SENAO CRIA
        if(!@is_dir($dirCompleto)){

            if(!@mkdir($dirCompleto)){
                $erro = "&raquo; N&atilde;o foi poss&iacute;vel encontrar ou criar o diret&oacute;rio <u>".$dirCompleto."</u>.<br>";
            }else{
                @chmod($dirCompleto, 0777);
            }
        }		
		
		//ADICIONA PASTA RECURSO A PASTA DO CPF
		$dirCompleto = $dirCompleto."/recurso";
		
		//VERIFICA SE PASTA DO CPF EXISTE, SENAO CRIA
        if(!@is_dir($dirCompleto)){

            if(!@mkdir($dirCompleto)){
                $erro = "&raquo; N&atilde;o foi poss&iacute;vel encontrar ou criar o diret&oacute;rio <u>".$dirCompleto."</u>.<br>";
            }else{
                @chmod($dirCompleto, 0777);
            }
        }	

        if(!empty($erro)){
                $erro .= "&nbsp; &nbsp;Arquivo n&atilde;o anexado.<br><br>";
                $arrResultado['descricao_erro'] = "<font color='red'>".$erro."</font>";
                $arrResultado['erro'] = "true"; 
                return $arrResultado;
        }else{
                $arrResultado['diretorio_completo'] = $dirCompleto;
                $arrResultado['erro'] = "false"; 
                return $arrResultado;	
        }
    }
    
    
    public function avaliacaoRecursoFaseUmAction()
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
    }
    
    
    
    public function listarRecursosFaseUmAction() {
        
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
        if(!empty($idSetorial)){
            $arrBusca["us.id_setorial = ?"] = $idSetorial;
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
        if(!empty($_POST['tipoInscricao'])){
            $arrBusca["c.int_tipocadastro = ?"] = $_POST['tipoInscricao'];
        }
        /*$avaliacaoPresidente = @$_POST['avaliacaoPresidente'];
        if(isset($avaliacaoPresidente) && $avaliacaoPresidente != "-1"){
            if($_POST['avaliacaoPresidente'] == '3'){
                $arrBusca["c.bol_validacaofinal IS NULL"] = "(?)";
            }else{
                $arrBusca["c.bol_validacaofinal IS NOT NULL AND c.bol_validacaofinal = ".$_POST['avaliacaoPresidente']] = "(?)";
            }
        }*/
        $arrBusca["u.id_perfil = ?"] = 1;
        //$arrBusca["c.int_tipocadastro = ?"] = 1;

        $tblRecurso = new Recurso();
        $total = 0;
        //$total = $tblRecurso->buscaRecurso($arrBusca, array(), null, null, false,true);
        $total = $tblRecurso->buscarRecurso($arrBusca, array(), null, null, true);

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) $fim = $total;

        $ordem = array();
        if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }else{$ordem = array('22 ASC');}
        $rs = $tblRecurso->buscarRecurso($arrBusca, $ordem, $tamanho, $inicio);

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

    public function formAvaliacaoRecursosFaseUmAction(){
        
        $idRecurso = $this->_request->getParam("id"); // pega o id via get
        
        $auth  = Zend_Auth::getInstance();
        $this->view->idUsuarioAvaliador = $auth->getIdentity()->id_usuario;
        $this->view->idPerfil           = $auth->getIdentity()->id_perfil;
        
        if(!empty($idRecurso)){
            
            $tblRecurso = new Recurso();
            $arrBusca = array();
            $arrBusca['id_recurso = ?'] = $idRecurso;
            $rsRecurso = $tblRecurso->buscar($arrBusca)->current(); 
            $this->view->dadosRecurso = $rsRecurso;
            
            if(!empty($rsRecurso))
            {
                $tblCadastro = new Cadastro();
                $arrBusca = array();
                $arrBusca['c.id_cadastro = ?'] = $rsRecurso->id_cadastro;
                $rsCadastro = $tblCadastro->buscaCadastro($arrBusca); 
                $this->view->dadosCadastro = $rsCadastro->current();
                
                $tbAnexoRecurso = new AnexoRecurso();
                $arrBusca = array();
                $arrBusca['id_recurso = ?'] = $idRecurso;
                $rsAnexoRecurso = $tbAnexoRecurso->buscar($arrBusca); 
                $this->setarValoresCamposViewRecurso($rsAnexoRecurso,$rsCadastro);
                
            }else{
                parent::message("Nenhum Recurso encontrado com os parâmetros informados.", "/recurso/avaliacao-recurso-fase-um", "ERROR");
            }
            
        }else{
            parent::message("Nenhum Recurso encontrado com os parâmetros informados.", "/recurso/avaliacao-recurso-fase-um", "ERROR");
        }
    }
    
    
    public function salvarAvaliacaoRecursoFaseUmAction(){
        
        $idCadastro = $this->_request->getParam("idRecurso"); 
        
        $post = Zend_Registry::get('post');
        
        $idRecurso             = $post->idRecurso;
        $idUsuarioAvaliador    = $post->idUsuarioAvaliador;
        $int_tipocadastro      = $post->int_tipocadastro;
        
        $avaliacaoEleitor      = $post->rd_avaliacao_fase1_eleitor;
        $obsAvaliacaoEleitor   = $post->obs_validacao_fase1_eleitor;
        
        $avaliacaoCandidato    = $post->rd_avaliacao_fase1_candidato;
        $obsAvaliacaoCandidato = $post->obs_validacao_fase1_candidato;
        
        if(!empty($idRecurso))
        {
            $db = Zend_Registry :: get('db');
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);

            try{
                $db->beginTransaction();
                
                $tblRecurso = new Recurso();

                $dadosAvaliacao= array();
                $dadosAvaliacao['chr_validacao_fase1_eleitor'] = $avaliacaoEleitor;
                $dadosAvaliacao['dte_validacao_fase1_eleitor'] = new Zend_Db_Expr('GETDATE()');
                $dadosAvaliacao['vhr_validacao_fase1_eleitor'] = $obsAvaliacaoEleitor;

                if($int_tipocadastro == 2){
                    $dadosAvaliacao['chr_validacao_fase1_candidato'] = $avaliacaoCandidato;
                    $dadosAvaliacao['dte_validacao_fase1_candidato'] = new Zend_Db_Expr('GETDATE()');
                    $dadosAvaliacao['vhr_validacao_fase1_candidato'] = $obsAvaliacaoCandidato;
                }

                $dadosAvaliacao["id_usuario_avaliador_fase1"] = $idUsuarioAvaliador;

                $where = array();
                $where['id_recurso = ?'] = $idRecurso;
                
                $tblRecurso->alterar($dadosAvaliacao, $where);
                
                $db->commit();
                parent::message("Avaliação de Recurso registrada com sucesso", "/recurso/avaliacao-recurso-fase-um", "CONFIRM");

            }catch(Exception $e){
                $db->rollBack();
                parent::message("Erro ao registrar a avalição do Recurso, tente novamente mais tarde. ".$e->getMessage(), "/recurso/avaliacao-recurso-fase-um", "ERROR");
            }
            
        }else{
            parent::message("Recurso não informado", "/recurso/avaliacao-recurso-fase-um", "ERROR");
        }
    }
    
    
    public function avaliacaoRecursoFaseDoisAction()
    {
        
        $auth  = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
            //$this->_forward("login", "login");
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }else{
            //perfil minico que pode acessa essa pagina
            parent::perfilMinimo(4); //perfil adminitrador e comissao nacional
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
    }
    
    
    
    public function listarRecursosFaseDoisAction() {
        
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
        if(!empty($idSetorial)){
            $arrBusca["us.id_setorial = ?"] = $idSetorial;
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
        if(!empty($_POST['tipoInscricao'])){
            $arrBusca["c.int_tipocadastro = ?"] = $_POST['tipoInscricao'];
        }
        
        $arrBusca["r.chr_validacao_fase1_eleitor IS NOT NULL"] = "(?)";//SOMENTE RECURSO ANALISADO NA PRIMEIRA INSTANCIA (FASE 1)
		$arrBusca["(r.chr_validacao_fase1_eleitor = 2 OR r.chr_validacao_fase1_candidato = 2)"] = "?";//SOMENTE CADASTROS NAO VALIDADOS (FASE 1) 
        $arrBusca["u.id_perfil = ?"] = 1;

        $tblRecurso = new Recurso();
        $total = 0;
        //$total = $tblRecurso->buscaRecurso($arrBusca, array(), null, null, false,true);
        $total = $tblRecurso->buscarRecurso($arrBusca, array(), null, null, true);

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) $fim = $total;

        $ordem = array();
        if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }else{$ordem = array('22 ASC');}
        $rs = $tblRecurso->buscarRecurso($arrBusca, $ordem, $tamanho, $inicio);

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

    public function formAvaliacaoRecursosFaseDoisAction(){
        
        $idRecurso = $this->_request->getParam("id"); // pega o id via get
        
        $auth  = Zend_Auth::getInstance();
        $this->view->idUsuarioAvaliador = $auth->getIdentity()->id_usuario;
        $this->view->idPerfil           = $auth->getIdentity()->id_perfil;
        
        if(!empty($idRecurso)){
            
            $tblRecurso = new Recurso();
            $arrBusca = array();
            $arrBusca['id_recurso = ?'] = $idRecurso;
            $rsRecurso = $tblRecurso->buscar($arrBusca)->current(); 
            $this->view->dadosRecurso = $rsRecurso;
            
            if(!empty($rsRecurso))
            {
                $tblCadastro = new Cadastro();
                $arrBusca = array();
                $arrBusca['c.id_cadastro = ?'] = $rsRecurso->id_cadastro;
                $rsCadastro = $tblCadastro->buscaCadastro($arrBusca); 
                $this->view->dadosCadastro = $rsCadastro->current();
                
                $tbAnexoRecurso = new AnexoRecurso();
                $arrBusca = array();
                $arrBusca['id_recurso = ?'] = $idRecurso;
                $rsAnexoRecurso = $tbAnexoRecurso->buscar($arrBusca); 
                $this->setarValoresCamposViewRecurso($rsAnexoRecurso,$rsCadastro);
            
            }else{
                parent::message("Nenhum Recurso encontrado com os parâmetros informados.", "/recurso/avaliacao-recurso-fase-dois", "ERROR");
            }
            
        }else{
            parent::message("Nenhum Recurso encontrado com os parâmetros informados.", "/recurso/avaliacao-recurso-fase-dois", "ERROR");
        }
    }
    
    
    public function salvarAvaliacaoRecursoFaseDoisAction(){
        
        $idCadastro = $this->_request->getParam("idRecurso"); 
        
        $post = Zend_Registry::get('post');
        
        $idRecurso             = $post->idRecurso;
        $idUsuarioAvaliador    = $post->idUsuarioAvaliador;
        $int_tipocadastro      = $post->int_tipocadastro;
        
        $avaliacaoEleitor      = $post->rd_avaliacao_fase2_eleitor;
        $obsAvaliacaoEleitor   = $post->obs_validacao_fase2_eleitor;
        
        $avaliacaoCandidato    = $post->rd_avaliacao_fase2_candidato;
        $obsAvaliacaoCandidato = $post->obs_validacao_fase2_candidato;
        
        if(!empty($idRecurso))
        {
            $db = Zend_Registry :: get('db');
            $db->setFetchMode(Zend_DB :: FETCH_OBJ);

            try{
                //$db->beginTransaction();
                
                $tblRecurso = new Recurso();

                $dadosAvaliacao= array();
                $dadosAvaliacao['chr_validacao_fase2_eleitor'] = $avaliacaoEleitor;
                $dadosAvaliacao['dte_validacao_fase2_eleitor'] = new Zend_Db_Expr('GETDATE()');
                $dadosAvaliacao['vhr_validacao_fase2_eleitor'] = $obsAvaliacaoEleitor;

                if($int_tipocadastro == 2){
                    $dadosAvaliacao['chr_validacao_fase2_candidato'] = $avaliacaoCandidato;
                    $dadosAvaliacao['dte_validacao_fase2_candidato'] = new Zend_Db_Expr('GETDATE()');
                    $dadosAvaliacao['vhr_validacao_fase2_candidato'] = $obsAvaliacaoCandidato;
                }

                $dadosAvaliacao["id_usuario_avaliador_fase2"] = $idUsuarioAvaliador;

                $where = array();
                $where['id_recurso = ?'] = $idRecurso;
                
                $tblRecurso->alterar($dadosAvaliacao, $where);
                
                //$db->commit();
                parent::message("Avaliação registrada com sucesso", "/recurso/avaliacao-recurso-fase-dois", "CONFIRM");

            }catch(Exception $e){
                //$db->rollBack();
                parent::message("Erro ao registrar a avalição, tente novamente mais tarde. ".$e->getMessage(), "/recurso/avaliacao-recurso-fase-dois", "ERROR");
            }
            
        }else{
            parent::message("Recurso não informado", "/recurso/avaliacao-recurso-fase-dois", "ERROR");
        }
    }

    /****************************************************************************/

    public function atualizarInformacoesRecursoAction()
    {

        $auth  = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
            //$this->_forward("login", "login");
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }else{
            //perfil minico que pode acessa essa pagina
            parent::perfilMinimo(4); //perfil componente da comissao
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
    }



    public function listarInscritosParaAtualizacaoAction() {

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
        if(!empty($idSetorial)){
            $arrBusca["us.id_setorial = ?"] = $idSetorial;
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
        if(!empty($_POST['tipoInscricao'])){
            $arrBusca["c.int_tipocadastro = ?"] = $_POST['tipoInscricao'];
        }
        /*$avaliacaoPresidente = @$_POST['avaliacaoPresidente'];
        if(isset($avaliacaoPresidente) && $avaliacaoPresidente != "-1"){
            if($_POST['avaliacaoPresidente'] == '3'){
                $arrBusca["c.bol_validacaofinal IS NULL"] = "(?)";
            }else{
                $arrBusca["c.bol_validacaofinal IS NOT NULL AND c.bol_validacaofinal = ".$_POST['avaliacaoPresidente']] = "(?)";
            }
        }*/
        $arrBusca["u.id_perfil = ?"] = 1;
        //$arrBusca["c.int_tipocadastro = ?"] = 1;

        $tblRecurso = new Recurso();
        $total = 0;
        //$total = $tblRecurso->buscaRecurso($arrBusca, array(), null, null, false,true);
        $total = $tblRecurso->buscarRecurso($arrBusca, array(), null, null, true);

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) $fim = $total;

        $ordem = array();
        if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }else{$ordem = array('22 ASC');}
        $rs = $tblRecurso->buscarRecurso($arrBusca, $ordem, $tamanho, $inicio);

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

    public function formAtualizarInformacaoRecursoAction(){

        $idRecurso = $this->_request->getParam("id"); // pega o id via get

        $auth  = Zend_Auth::getInstance();
        $this->view->idUsuarioAvaliador = $auth->getIdentity()->id_usuario;
        $this->view->idPerfil           = $auth->getIdentity()->id_perfil;

        $arrTipoArquivo = array();
        $arrTipoArquivo['18'] = "Comprovante Atuação Setor";
        $arrTipoArquivo['19'] = "Identidade";
        $arrTipoArquivo['20'] = "Cpf";
        $arrTipoArquivo['21'] = "Comprovante de Residência";
        $arrTipoArquivo['27'] = "Comprovante Função Cargo Comissionado";
        $arrTipoArquivo['29'] = "Currículo";
        $arrTipoArquivo['30'] = "Portfolio";
        $arrTipoArquivo['32'] = "Carta Apoio";
        $arrTipoArquivo['35'] = "Carta Programa (versão em arquivo)";
        $this->view->tipoArquivos = $arrTipoArquivo;
        
        if(!empty($idRecurso)){

            $tblRecurso = new Recurso();
            $arrBusca = array();
            $arrBusca['id_recurso = ?'] = $idRecurso;
            $rsRecurso = $tblRecurso->buscar($arrBusca)->current();
            $this->view->dadosRecurso = $rsRecurso;

            if(!empty($rsRecurso))
            {
                $tblCadastro = new Cadastro();
                $arrBusca = array();
                $arrBusca['c.id_cadastro = ?'] = $rsRecurso->id_cadastro;
                $rsCadastro = $tblCadastro->buscaCadastro($arrBusca);
                $this->view->dadosCadastro = $rsCadastro->current();

                $tbAnexoRecurso = new AnexoRecurso();
                $arrBusca = array();
                $arrBusca['id_recurso = ?'] = $idRecurso;
                $rsAnexoRecurso = $tbAnexoRecurso->buscar($arrBusca);
                $this->setarValoresCamposViewRecurso($rsAnexoRecurso,$rsCadastro);
                

            }else{
                parent::message("Nenhum Recurso encontrado com os parâmetros informados.", "/recurso/avaliacao-recurso-fase-um", "ERROR");
            }

        }else{
            parent::message("Nenhum Recurso encontrado com os parâmetros informados.", "/recurso/avaliacao-recurso-fase-um", "ERROR");
        }
    }


    public function salvarAtualizacaoInformacoesRecursoAction(){

        $idCadastro = $this->_request->getParam("idRecurso");

        $post = Zend_Registry::get('post');
        $idRecurso = $post->idRecurso;
        $tbAnexoRecurso = new AnexoRecurso();

        try{
            
            foreach($_POST as $chave => $valor){

                $prefixo = substr($chave, 0, 15);

                if($prefixo == "sltTipoArquivo_"){
                    
                    $arrTemp = explode("_",$valor);
                    
                    //if(count($arrTemp)>0){
                        
                        $id_anexo_recurso = $arrTemp[0];
                        $id_item = $arrTemp[1];

                        $dados["id_item"] = $id_item;

                        $where = array();
                        $where['id_anexo_recurso = ?'] = $id_anexo_recurso;
                        $where['id_recurso = ?'] = $idRecurso;
                        
                        $tbAnexoRecurso->alterar($dados, $where);
                    //}
                }
            }
            //xd('para');
            parent::message("Informações atualizadas com sucesso", "/recurso/atualizar-informacoes-recurso", "CONFIRM");

        }catch(Exception $e){

            parent::message("Erro ao atualizar informações, tente novamente mais tarde. ".$e->getMessage(), "/recurso/atualizar-informacoes-recurso", "ERROR");
        }
    }


    
} // fecha class