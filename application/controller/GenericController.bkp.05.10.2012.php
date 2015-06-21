<?php
/**
 * Controle Genérico (Utilizado por todos os controles)
 * Trata as mensagens do sistema
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class GenericController extends Zend_Controller_Action
{
	/**
	 * Variável com a mensagem
	 * @var $_msg
	 */
	protected $_msg;



	/**
	 * Variável com a página de redirecionamento
	 * @var $_url
	 */
	protected $_url;



	/**
	 * Variável com o tipo de mensagem
	 * Valores: ALERT, CONFIRM, ERROR ou vazio
	 * @var $_type
	 */
	protected $_type;



	/**
	 * Variável com a URL padrao do sistema
	 * @var $_urlPadrao
	 */
	protected $_urlPadrao;


	private  $idResponsavel  		= 0;
    private  $idAgente 			= 0;
	private  $idUsuario 			= 0;
	private $diretorioUpload = null;
    private $caminhoAcessoArquivo = null;
    
	/**
	 * Reescreve o método init() para aceitar 
	 * as mensagens e redirecionamentos. 
	 * Teremos que chamá-lo dentro do 
	 * método init() da classe filha assim: parent::init();
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		$auth  = Zend_Auth::getInstance();
		$this->view->usuario = $auth->getIdentity();
		$this->_msg  = $this->_helper->getHelper('FlashMessenger');
		$this->_url  = $this->_helper->getHelper('Redirector');
		$this->_type = $this->_helper->getHelper('FlashMessengerType');
		
		$this->diretorioUpload = getcwd() . "/public/anexos/";
		$this->caminhoAcessoArquivo = Zend_Controller_Front::getInstance()->getBaseUrl() . "/public/anexos/";

		//if($auth->hasIdentity() && $auth->getIdentity()->id_perfil != 4){ //real
		//if(date('YmdHis') > '20120904235959'){ //real
		//if(date('YmdHis') > '20120904214300'){ //teste
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
							<h2 style="font-family:Verdana;">Inscrições encerradas</h2>
						</td>
					 </tr>
					 </table>
					 <center><center>
					';
			//echo $html;
			//die;
		//}   
		
		
		//echo "&nbsp;&nbsp;&nbsp;".date('YmdHis');
		if(date('YmdHis') < '20120821235959'){ //real
		//if(date('YmdHis') < '20120821160000'){ //teste
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
							<h2 style="font-family:Verdana;">Inscrições abertas a partir <br>das 00h:00 do dia 22/08/2012</h2>
						</td>
					 </tr>
					 </table>
					 <center><center>
					';
			echo $html;
			die;
		}
		if(date('YmdHis') > '20120826235959'){  //real
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
							<h2 style="font-family:Verdana;">Inscrições encerradas.</h2>
						</td>
					 </tr>
					 </table>
					 <center><center>
					';
			//echo $html;
			//die;
		}
		/******************************************************************************************************************************/

		  /*@$cpf = isset($auth->getIdentity()->usu_codigo) ? $auth->getIdentity()->usu_identificacao : $auth->getIdentity()->Cpf;        
	      
		  if ($cpf):
		        
		        // Busca na SGCAcesso
		        $sgcAcesso 	 = new Sgcacesso();
		        $buscaAcesso = $sgcAcesso->buscar(array('Cpf = ?' => $cpf));
		        
		        // Busca na Usuarios
		        $usuarioDAO   = new Usuario();
		        $buscaUsuario = $usuarioDAO->buscar(array('usu_identificacao = ?' => $cpf));
		
		        // Busca na Agentes
		        $agentesDAO  = new Agentes();
		        $buscaAgente = $agentesDAO->BuscaAgente($cpf);
		        
		        if( count($buscaAcesso) > 0){ $this->idResponsavel = $buscaAcesso[0]->IdUsuario; }
		        if( count($buscaAgente) > 0 ){ $this->idAgente 	   = $buscaAgente[0]->idAgente; }
		        if( count($buscaUsuario) > 0 ){ $this->idUsuario   = $buscaUsuario[0]->usu_codigo; }
		        
		        $this->view->idAgenteKeyLog 		= $this->idAgente;
		        $this->view->idResponsavelKeyLog 	= $this->idResponsavel;
		        $this->view->idUsuarioKeyLog 		= $this->idUsuario;
	        
	      endif;*/
		        
        /****************************************************************************************************************************/                

                
	} // fecha init()



	/**
	 * Método para chamar as mensagens e fazer o redirecionamento
	 * @access protected
	 * @param string $msg
	 * @param string $url
	 * @param string $type
	 * @return void
	 */
	protected function message($msg, $url, $type = null)
	{
		$this->_helper->viewRenderer->setNoRender(true);
		$this->_helper->flashMessenger->addMessage($msg);
		$this->_helper->flashMessengerType->addMessage($type);
		$this->_redirect($url);
		exit();
	} // fecha message()



	/**
	 * Reescreve o método postDispatch() que é responsável 
	 * por executar uma ação após a execução de um método
	 * @access public
	 * @param void
	 * @return void
	 */
	public function postDispatch()
	{
		if ($this->_msg->hasMessages())
		{
			$this->view->message = implode("<br />", $this->_msg->getMessages());
		}
		if ($this->_type->hasMessages())
		{
			$this->view->message_type = implode("<br />", $this->_type->getMessages());
		}
		parent::postDispatch(); // chama o método pai
	} // fecha postDispatch()
        
	public function perfilMinimo($idPerfilMinimo)
	{
            $auth  = Zend_Auth::getInstance();
            $idPerfil = $auth->getIdentity()->id_perfil;
            if($idPerfil < $idPerfilMinimo){
                
                $this->message("Você não tem permissão para acessar essa área do sistema!", "admin", "ALERT");
            }
	} // fecha postDispatch()



	/**
	 * Método responsável pela autenticação e perfis
	 * @access protected
	 * @param integer $tipo
	 * 		0 => somente autenticação zend
	 * 		1 => autenticação e permissões zend (AMBIENTE MINC)
	 * 		2 => autenticação scriptcase (AMBIENTE PROPONENTE)
	 * 		3 => autenticação scriptcase e autenticação/permissão zend (AMBIENTE PROPONENTE E MINC)
	 * @param array $permissoes (array com as permissões para acesso)
	 * @return void
	 */
	protected function perfil($tipo = 0, $permissoes = null)
	{
		$auth         = Zend_Auth::getInstance(); // pega a autenticação
		$Usuario      = new Usuario(); // objeto usuário
		$UsuarioAtivo = new Zend_Session_Namespace('UsuarioAtivo'); // cria a sessão com o usuário ativo
		$GrupoAtivo   = new Zend_Session_Namespace('GrupoAtivo'); // cria a sessão com o grupo ativo
	
		// somente autenticação zend
		if ($tipo == 0 || empty($tipo))
		{
			if ($auth->hasIdentity()) // caso o usuário esteja autenticado
			{
				// pega as unidades autorizadas, orgãos e grupos do usuário (pega todos os grupos)
				if (isset($auth->getIdentity()->usu_codigo) && !empty($auth->getIdentity()->usu_codigo))
				{
					$grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
                                $Agente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
                                $idAgente = $Agente['idAgente'];
                                $Cpflogado = $Agente['usu_identificacao'];
				}
				else
				{
					return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
				}

				// manda os dados para a visão
                                $this->view->idAgente    = $idAgente;
				$this->view->usuario     = $auth->getIdentity(); // manda os dados do usuário para a visão
				$this->view->arrayGrupos = $grupos; // manda todos os grupos do usuário para a visão
				$this->view->grupoAtivo  = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuário para a visão
				$this->view->orgaoAtivo  = $GrupoAtivo->codOrgao; // manda o órgão ativo do usuário para a visão
			} // fecha if
			else // caso o usuário não esteja autenticado
			{
				return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
			}
		} // fecha if
		// autenticação e permissões zend (AMBIENTE MINC)
		else if ($tipo === 1)
		{
			if ($auth->hasIdentity()) // caso o usuário esteja autenticado
			{
				if (!in_array($GrupoAtivo->codGrupo, $permissoes)) // verifica se o grupo ativo está no array de permissões
				{
					$this->message("Você não tem permissão para acessar essa área do sistema!", "principal/index", "ALERT");
				}

				// pega as unidades autorizadas, orgãos e grupos do usuário (pega todos os grupos)
				$grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);

				// manda os dados para a visão
                                $Agente = $Usuario->getIdUsuario($auth->getIdentity()->usu_codigo);
                                $idAgente = $Agente['idAgente'];
				$this->view->usuario     = $auth->getIdentity(); // manda os dados do usuário para a visão
				$this->view->arrayGrupos = $grupos; // manda todos os grupos do usuário para a visão
				$this->view->grupoAtivo  = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuário para a visão
				$this->view->orgaoAtivo  = $GrupoAtivo->codOrgao; // manda o órgão ativo do usuário para a visão
			} // fecha if
			else // caso o usuário não esteja autenticado
			{
				return $this->_helper->redirector->goToRoute(array('controller' => 'index', 'action' => 'logout'), null, true);
			}
		} // fecha else if


		// autenticação scriptcase (AMBIENTE PROPONENTE)
		else if ($tipo == 2)
		{
			// configurações do layout padrão para o scriptcase
			Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));

			// pega o id do usuário logado pelo scriptcase (sessão)
			//$codUsuario = isset($_SESSION['gusuario']['id']) ? $_SESSION['gusuario']['id'] : $UsuarioAtivo->codUsuario;
			$codUsuario = isset($_GET['idusuario']) ? (int) $_GET['idusuario'] : $UsuarioAtivo->codUsuario;
			//$codUsuario = 366;
			if (isset($codUsuario) && !empty($codUsuario))
			{
				$UsuarioAtivo->codUsuario = $codUsuario;
			}
			else // caso o usuário não esteja autenticado
			{
				$this->message("Você não tem permissão para acessar essa área do sistema!", "index", "ALERT");
			}

			// tenta fazer a autenticação do usuário logado no scriptcase para o zend
			$autenticar = UsuarioDAO::loginScriptcase($codUsuario);

			if ($autenticar && $auth->hasIdentity()) // caso o usuário seja passado pelo scriptcase e esteja autenticado
			{
				// manda os dados para a visão
				$this->view->usuario = $auth->getIdentity(); // manda os dados do usuário para a visão
			} // fecha if
			else // caso o usuário não esteja autenticado
			{
				$this->message("Você não tem permissão para acessar essa área do sistema!", "index", "ALERT");
			}
		} // fecha else if


		// autenticação scriptcase e autenticação/permissão zend (AMBIENTE PROPONENTE E MINC)
		else if ($tipo == 3)
		{

			// ========== INÍCIO AUTENTICAÇÃO SCRIPTCASE ==========
			// pega o id do usuário logado pelo scriptcase
			//$codUsuario = isset($_SESSION['gusuario']['id']) ? $_SESSION['gusuario']['id'] : $UsuarioAtivo->codUsuario;
			$codUsuario = isset($_GET['idusuario']) ? (int) $_GET['idusuario'] : $UsuarioAtivo->codUsuario;
			//$codUsuario = 366;
			if (isset($codUsuario) && !empty($codUsuario))
			{
				// configurações do layout padrão para o scriptcase
				Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));

				$UsuarioAtivo->codUsuario = $codUsuario;

				// tenta fazer a autenticação do usuário logado no scriptcase para o zend
				$autenticar = UsuarioDAO::loginScriptcase($codUsuario);

				if ($autenticar && $auth->hasIdentity()) // caso o usuário seja passado pelo scriptcase e esteja autenticado
				{
					// manda os dados para a visão
					$this->view->usuario = $auth->getIdentity(); // manda os dados do usuário para a visão
				} // fecha if
				else // caso o usuário não esteja autenticado
				{
					$this->message("Você não tem permissão para acessar essa área do sistema!", "index", "ALERT");
				}
			} // fecha if
			// ========== FIM AUTENTICAÇÃO SCRIPTCASE ==========


			// ========== INÍCIO AUTENTICAÇÃO ZEND ==========
			else // caso o usuário não esteja autenticado pelo scriptcase
			{
				if (!in_array($GrupoAtivo->codGrupo, $permissoes)) // verifica se o grupo ativo está no array de permissões
				{
					$this->message("Você não tem permissão para acessar essa área do sistema!", "principal/index", "ALERT");
				}

				// pega as unidades autorizadas, orgãos e grupos do usuário (pega todos os grupos)
				if (isset($auth->getIdentity()->usu_codigo) && !empty($auth->getIdentity()->usu_codigo))
				{
					$grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
				}
				else
				{
					$this->message("Você não tem permissão para acessar essa área do sistema!", "principal/index", "ALERT");
				}

				// manda os dados para a visão
				$this->view->usuario     = $auth->getIdentity(); // manda os dados do usuário para a visão
				$this->view->arrayGrupos = $grupos; // manda todos os grupos do usuário para a visão
				$this->view->grupoAtivo  = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuário para a visão
				$this->view->orgaoAtivo  = $GrupoAtivo->codOrgao; // manda o órgão ativo do usuário para a visão
			} // fecha else
		} // fecha else if

		// autenticação migracao e autenticação/permissão zend (AMBIENTE DE MIGRAÇ?O E MINC)
		else if ($tipo == 4)
		{

			// ========== INÍCIO AUTENTICAÇÃO MIGRAÇ?O ==========
			// pega o id do usuário logado pelo scriptcase
			//$codUsuario = isset($_SESSION['gusuario']['id']) ? $_SESSION['gusuario']['id'] : $UsuarioAtivo->codUsuario;
			$codUsuario = isset($auth->getIdentity()->IdUsuario) ? (int) $auth->getIdentity()->IdUsuario : $UsuarioAtivo->codUsuario;
			//$codUsuario = 366;
			if (isset($codUsuario) && !empty($codUsuario))
			{
				// configurações do layout padrão para o proponente
				Zend_Layout::startMvc(array('layout' => 'layout_proponente'));

				$UsuarioAtivo->codUsuario = $codUsuario;

				// tenta fazer a autenticação do usuário logado no scriptcase para o zend
				$autenticar = UsuarioDAO::loginScriptcase($codUsuario);

				if ($autenticar && $auth->hasIdentity()) // caso o usuário seja passado pelo scriptcase e esteja autenticado
				{
					// manda os dados para a visão
					$this->view->usuario = $auth->getIdentity(); // manda os dados do usuário para a visão
				} // fecha if
				else // caso o usuário não esteja autenticado
				{
					$this->message("Você não tem permissão para acessar essa área do sistema!", "index", "ALERT");
				}
			} // fecha if
			// ========== FIM AUTENTICAÇÃO MIGRAÇ?O ==========


			// ========== INÍCIO AUTENTICAÇÃO ZEND ==========
			else // caso o usuário não esteja autenticado pelo scriptcase
			{
				if (!in_array($GrupoAtivo->codGrupo, $permissoes)) // verifica se o grupo ativo está no array de permissões
				{
					$this->message("Você não tem permissão para acessar essa área do sistema!", "principal/index", "ALERT");
				}

				// pega as unidades autorizadas, orgãos e grupos do usuário (pega todos os grupos)
				if (isset($auth->getIdentity()->usu_codigo) && !empty($auth->getIdentity()->usu_codigo))
				{
					$grupos = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21);
				}
				else
				{
					$this->message("Você não tem permissão para acessar essa área do sistema!", "principal/index", "ALERT");
				}

				// manda os dados para a visão
				$this->view->usuario     = $auth->getIdentity(); // manda os dados do usuário para a visão
				$this->view->arrayGrupos = $grupos; // manda todos os grupos do usuário para a visão
				$this->view->grupoAtivo  = $GrupoAtivo->codGrupo; // manda o grupo ativo do usuário para a visão
				$this->view->orgaoAtivo  = $GrupoAtivo->codOrgao; // manda o órgão ativo do usuário para a visão
			} // fecha else
		} // fecha else if
		// ========== FIM AUTENTICAÇÃO ZEND ==========

                if(!empty($grupos)){
                    $tblSGCacesso = new Sgcacesso();
                    $rsSGCacesso = $tblSGCacesso->buscar(array("Cpf = ? "=>$auth->getIdentity()->usu_identificacao));
                    if($rsSGCacesso->count() > 0){
                        $this->view->arrayGrupoProponente = array("gru_codigo"=>1111, "uog_orgao"=>2222, "gru_nome"=>"Proponente");
                    }
                }

	} // fecha método perfil()

        /**
         * Monta a tela de retorno ao usuario
         * @param string $corpo - arquivo tpl do corpo
         * @param array $dados - array com os dados a serem inseridos na tela, no seguinte formato "nome"=>"valor"
         * @param boolean $exibeHeader - true ou false para exibir header, menu e rodape
         * @return void
         */
        public function montaTela($view, $dados=array())
        {
            // percorrendo array de dados e inserindo no template
            foreach ($dados as $dado=>$valor)
            {
                    $this->view->assign($dado, $valor);
            }

            // retorna o tempalte master, com corpo e variaveis setadas
            $this->renderScript($view);
        }

        /**
         * Recebe codigo em HTML e gera um PDF
         * @return void
         */
        public function gerarPdfAction() {
            @ini_set("memory_limit", "5000M");
            @ini_set('max_execution_time', 3000);
            @set_time_limit(9500);
            @error_reporting(0);

            $this->_helper->layout->disableLayout();
            @$this->_helper->viewRenderer->setNoRender();

            @$output = '
                            <style>
                                    th{
                                    background:#ABDA5D;
                                    color:#3A7300;
                                    text-transform:uppercase;
                                    font-size:14px;
                                    font-weight: bold;
                                    font-family: sans-serif;
                                    height: 16px;
                                    line-height: 16px;
                            }
                            td{
                                    color:#000;
                                    font-size:14px;
                                    font-family: sans-serif;
                                    height: 14px;
                                    line-height: 14px;
                            }
                            .destacar{
                                    background:#DFEFC2;
                            }
                            .blue{
                                    color: blue;
                            }
                            .red{
                                    color: red;
                            }
                            .orange{
                                    color: orange;
                            }
                            .green{
                                    color: green;
                            }

                            .direita{
                                    text-align: right;
                            }

                            .centro{
                                    text-align: center;
                            }

                            </style>';



            @$output .= $_POST['html'];

            $patterns = array();
            $patterns[] = '/<table.*?>/is';
            $patterns[] = '/size="3px"/is';
            $patterns[] = '/size="4px"/is';
            $patterns[] = '/size="2px"/is';
            $patterns[] = '/<thead>/is';
            $patterns[] = '/<\/thead>/is';
            $patterns[] = '/<tbody>/is';
            $patterns[] = '/<\/tbody>/is';
            $patterns[] = '/<col.*?>/is';
            $patterns[] = '/<a.*?>/is';
            $patterns[] = '/<img.*?>/is';

            $replaces = array();
            $replaces[] = '<table cellpadding="0" cellspacing="1" border="1" width="90%" align="center">';
            $replaces[] = 'size="14px"';
            $replaces[] = 'size="14px"';
            $replaces[] = 'size="11px"';
            $replaces[] = '';
            $replaces[] = '';
            $replaces[] = '';
            $replaces[] = '';
            $replaces[] = '';
            $replaces[] = '';
            $replaces[] = '';

            //ANTIGO METODO QUE GERAVA PDF UTILIZANDO A BIBLIOTECA DOMPDF
//            $output = preg_replace($patterns,$replaces,$output);
//            $pdf = new PDF($output, 'pdf');
//            $pdf->gerarRelatorio('h');
            //$this->view->dados = ManterpropostaeditalDAO::listarEditalResumo(array());

            //METODO QUE GERA PDF UTILIZANDO A BIBLIOTECA MPDF
            @$output = preg_replace($patterns,$replaces,utf8_encode($output));
            @$pdf=new mPDF('pt','A4',12,'',8,8,5,14,9,9,'P');
            @$pdf->allow_charset_conversion = true;
            @$pdf->charset_in='UTF-8';
            @$pdf->WriteHTML($output);
            @$pdf->Output();

            //DEBUG
//            $mtime = microtime();
//            $mtime = explode(" ",$mtime);
//            $mtime = $mtime[1] + $mtime[0];
//            $starttime = $mtime;
//            $mtime = microtime();
//            $mtime = explode(" ",$mtime);
//            $mtime = $mtime[1] + $mtime[0];
//            $endtime = $mtime;
//            $totaltime = ($endtime - $starttime);
//            $pdf->WriteHTML("PDF Gerado em ".$totaltime." segundos");


        }

        public function gerarXlsAction(){
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->layout->disableLayout();

            $html = $_POST['html'];
            header("Content-Type: application/vnd.ms-excel");
            header("Content-Disposition: inline; filename=file.xls;");
            echo $html;
        }
        
        
        public function html2PdfAction(){
            $orientacao = false;
            
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
        
            if($this->_getParam('orientacao') == 'L'){
                $orientacao = true;
            }
            
            $pdf = new PDFCreator($_POST['html'],$orientacao);
        
            $pdf->gerarPdf();
        }

        public static function montaBuscaData(Zend_Filter_Input $post, $tpBuscaData, $cmpData, $cmpBD, $cmpDataFinal = null, Array $arrayJoin = null ){
            $arrBusca = array();
            $aux1 = $post->__get($cmpData);
            $aux2 = $post->__get($tpBuscaData);
            if(!empty ($aux1) || $aux2 != ''){
                if($post->__get($tpBuscaData) == "igual"){
                    $arrBusca["{$cmpBD} >= ?"] = ConverteData($post->__get($cmpData), 13)." 00:00:00";
                    $arrBusca["{$cmpBD} <= ?"] = ConverteData($post->__get($cmpData), 13)." 23:59:59";

                }elseif($post->__get($tpBuscaData) == "maior"){
                    $arrBusca["{$cmpBD} >= ?"] = ConverteData($post->__get($cmpData), 13)." 00:00:00";

                }elseif($post->__get($tpBuscaData) == "menor"){
                    $arrBusca["{$cmpBD} <= ?"] = ConverteData($post->__get($cmpData), 13)." 00:00:00";

                }elseif($post->__get($tpBuscaData) == "entre"){
                    
                    $arrBusca["{$cmpBD} >= ?"] = ConverteData($post->__get($cmpData), 13)." 00:00:00";
                    $arrBusca["{$cmpBD} <= ?"] = ConverteData($post->__get($cmpDataFinal), 13)." 23:59:59";
                    
                }elseif($post->__get($tpBuscaData) == "OT"){
                    $arrBusca["{$cmpBD} = ?"] = date("Y-m-d",strtotime("-1 day"))." 00:00:00";

                }elseif($post->__get($tpBuscaData) == "U7"){
                    $arrBusca["{$cmpBD} > ?"] = date("Y-m-d",strtotime("-7 day"))." 00:00:00";
                    $arrBusca["{$cmpBD} < ?"] = date("Y-m-d")." 23:59:59";

                }elseif($post->__get($tpBuscaData) == "SP"){
                    /*$arrBusca["{$cmpBD} > ?"] = date("Y-m-").(date("d")-7)." 00:00:00";
                    $arrBusca["{$cmpBD} < ?"] = date("Y-m-d")." 23:59:59";*/
                    
                   $dia_semana = date('w');
                   $primeiro_dia = date('Y-m-d', strtotime("-".$dia_semana."day"));
                   $domingo = date('Y-m-d',  strtotime($primeiro_dia."-1 week"));
                   $sabado =  date('Y-m-d',  strtotime($domingo."6 day"));
                   
                   $arrBusca["{$cmpBD} >= ?"] = $domingo." 00:00:00";
                   $arrBusca["{$cmpBD} <= ?"] = $sabado." 23:59:59";
                   

                }elseif($post->__get($tpBuscaData) == "MM"){
                    $arrBusca["{$cmpBD} > ?"] = date("Y-m-01")." 00:00:00";
                    $arrBusca["{$cmpBD} < ?"] = date("Y-m-d")." 23:59:59";

                }elseif($post->__get($tpBuscaData) == "UM"){
                    $arrBusca["{$cmpBD} >= ?"] = date("Y-m",strtotime("-1 month"))."-01 00:00:00";
                    //$arrBusca["{$cmpBD} <= ?"] = date("d/m/Y", mktime(0, 0, 0, date("m",  strtotime("-1 month"))+1, 0, date("Y")));
                    $arrBusca["{$cmpBD} <= ?"] = date("Y-m-d", mktime(0, 0, 0, date("m",  strtotime("-1 month"))+1, 0, date("Y")));

                }elseif($post->__get($tpBuscaData) == ""){
                    
                }else{
                    $arrBusca["{$cmpBD} > ?"] = ConverteData($post->__get($cmpData), 13)." 00:00:00";

                    if($post->__get($cmpDataFinal) != ""){
                        $arrBusca["{$cmpBD} < ?"] = ConverteData($post->__get($cmpDataFinal), 13)." 23:59:59";
                    }
                }
            }

            if(!empty($arrayJoin)){
                $arrBusca = array_merge($arrayJoin, $arrBusca);
            }

            return $arrBusca;
        }
        
        
        public function prepararXlsPdfAction(){
            Zend_Layout::startMvc(array('layout' => 'layout_scriptcase'));
            ini_set('max_execution_time', 900);
            $this->_response->clearHeaders();  
            $dados = $this->_getAllParams();
            
            $this->view->dados = $dados;
            $this->view->tipo = $dados['tipo'];
            
            if($dados['view']){
                $this->montaTela($dados['view'],$dados);
            }
            
        }
        
        public function setarValoresCamposView($rsObj,$blnForum=false){
         //xd($rsObj);
            foreach($rsObj as $campo){
				$cpf = $campo->vhr_login;
                if($campo->id_item == 1){ //nome 
                    $this->view->nome = $campo->vhr_valor;
                    $this->view->rd_nome  = $campo->chr_validacao;
                    $this->view->obs_nome = $campo->vhr_observacao;
                }
                if($campo->id_item == 2){ //nome_artistico 
                    $this->view->nome_artistico = $campo->vhr_valor;
                    $this->view->rd_apelido  = $campo->chr_validacao;
                    $this->view->obs_apelido = $campo->vhr_observacao;
                }
                if($campo->id_item == 3){ //cpf 
                    $this->view->cpf = $campo->vhr_valor;
                    $this->view->rd_cpf  = $campo->chr_validacao;
                    $this->view->obs_cpf = $campo->vhr_observacao;
                }
                if($campo->id_item == 4){ //rg 
                    $this->view->rg = $campo->vhr_valor;
                    $this->view->rd_rg  = $campo->chr_validacao;
                    $this->view->obs_rg = $campo->vhr_observacao;
                }
                if($campo->id_item == 5){ //nascimento 
                    $this->view->nascimento = $campo->vhr_valor;
                    $this->view->rd_nascimento  = $campo->chr_validacao;
                    $this->view->obs_nascimento = $campo->vhr_observacao;
                }
                if($campo->id_item == 6){ //naturalidade
                    $this->view->naturalidade = $campo->vhr_valor;
                    $this->view->rd_naturalidade  = $campo->chr_validacao;
                    $this->view->obs_naturalidade = $campo->vhr_observacao;
                }
                if($campo->id_item == 7){ //email 
                    $this->view->email = $campo->vhr_valor;
                    $this->view->rd_email  = $campo->chr_validacao;
                    $this->view->obs_email = $campo->vhr_observacao;
                }
                if($campo->id_item == 8){ //endereco
                    $this->view->endereco = $campo->vhr_valor;
                    $this->view->rd_endereco  = $campo->chr_validacao;
                    $this->view->obs_endereco = $campo->vhr_observacao;
                }
                if($campo->id_item == 9){ //complemento
                    $this->view->complemento = $campo->vhr_valor;
                    $this->view->rd_complemento  = $campo->chr_validacao;
                    $this->view->obs_complemento = $campo->vhr_observacao;
                }
                if($campo->id_item == 10){ //bairro
                    $this->view->bairro = $campo->vhr_valor;
                    $this->view->rd_bairro  = $campo->chr_validacao;
                    $this->view->obs_bairro = $campo->vhr_observacao;
                }
                if($campo->id_item == 11){ //cep
                    $this->view->cep = $campo->vhr_valor;
                    $this->view->rd_cep  = $campo->chr_validacao;
                    $this->view->obs_cep = $campo->vhr_observacao;
                }
                if($campo->id_item == 12){ //cidade
                    $this->view->cidade = $campo->vhr_valor;
                    $this->view->rd_cidade  = $campo->chr_validacao;
                    $this->view->obs_cidade = $campo->vhr_observacao;
                }
                if($campo->id_item == 13){ //uf
                    $this->view->uf = $campo->vhr_valor;
                    $this->view->rd_uf  = $campo->chr_validacao;
                    $this->view->obs_uf = $campo->vhr_observacao;
                }
                if($campo->id_item == 14){ //formacao
                    $this->view->formacao = $campo->vhr_valor;
                    $this->view->rd_formacao  = $campo->chr_validacao;
                    $this->view->obs_formacao = $campo->vhr_observacao;
                }
                if($campo->id_item == 15){ //area_atuacao
                    $this->view->area_atuacao = $campo->vhr_valor;
                    $this->view->rd_area_atuacao  = $campo->chr_validacao;
                    $this->view->obs_area_atuacao = $campo->vhr_observacao;
                }
                if($campo->id_item == 16){ //apresentacao
                    $this->view->apresentacao = $campo->vhr_valor;
                    $this->view->rd_apresentacao  = $campo->chr_validacao;
                    $this->view->obs_apresentacao = $campo->vhr_observacao;
                }
                if($campo->id_item == 17){ //segmento
                    $this->view->segmento = $campo->vhr_valor;
                    $this->view->rd_segmento  = $campo->chr_validacao;
                    $this->view->obs_segmento = $campo->vhr_observacao;
                }
                if($campo->id_item == 18){ //file_comprovante_atuacao_setor
                    //$this->view->file_comprovante_atuacao_setor   = $campo->vhr_valor;
                    $arrRetorno = array();
                    $arrRetorno = $this->montaLinkArquivo($cpf,'comprovante_atuacao_setor.pdf',$blnForum);
                    //xd($arrRetorno);
                    $urlArquivo = $arrRetorno['urlArquivo'];
                    $link       = $arrRetorno['linkAcesso'];
                    //$urlArquivo = $this->diretorioUpload.'/'.$cpf.'/comprovante_atuacao_setor.pdf';
                    //$caminhoArquivo = $this->caminhoAcessoArquivo.$cpf.'/comprovante_atuacao_setor.pdf';
                    //$this->view->file_comprovante_atuacao_setor     = (file_exists($urlArquivo)) ? $caminhoArquivo : "";
                    /*$nome_arquivo = null;
                    $nome_arquivo = explode("/",$campo->vhr_valor);
                    $nome_arquivo = $nome_arquivo[count($nome_arquivo)-1];*/
                    $this->view->file_comprovante_atuacao_setor     = (file_exists($urlArquivo)) ? $link : "";
                    $this->view->arquivo_comprovante_atuacao_setor  = 'comprovante_atuacao_setor.pdf';//$nome_arquivo;
                    $this->view->idCadastroXItem_comprovante_atuacao_setor  = $campo->id_cadastro_item;
                    $this->view->rd_comprovante_atuacao_setor  = $campo->chr_validacao;
                    $this->view->obs_comprovante_atuacao_setor = $campo->vhr_observacao;
                }
                if($campo->id_item == 19){ //file_identidade
                    //$this->view->file_identidade = $campo->vhr_valor;
                    unset($arrRetorno);
                    unset($link);
                    unset($urlArquivo);
                    $arrRetorno = $this->montaLinkArquivo($cpf,'identidade.pdf',$blnForum);
                    $urlArquivo = $arrRetorno['urlArquivo'];
                    $link       = $arrRetorno['linkAcesso'];
                    $this->view->file_identidade = (file_exists($urlArquivo)) ? $link : "";
                    $this->view->arquivo_identidade  = 'identidade.pdf';//$nome_arquivo;
                    $this->view->idCadastroXItem_identidade  = $campo->id_cadastro_item;
                    $this->view->rd_identidade  = $campo->chr_validacao;
                    $this->view->obs_identidade = $campo->vhr_observacao;
                }
                if($campo->id_item == 20){ //file_cpf
                    //$this->view->file_cpf = $campo->vhr_valor;
                    unset($arrRetorno);
                    unset($link);
                    unset($urlArquivo);
                    $arrRetorno = $this->montaLinkArquivo($cpf,'cpf.pdf',$blnForum);
                    $urlArquivo = $arrRetorno['urlArquivo'];
                    $link       = $arrRetorno['linkAcesso'];
                    $this->view->file_cpf = (file_exists($urlArquivo)) ? $link : "";
                    $this->view->arquivo_cpf  = 'cpf.pdf';//$nome_arquivo;
                    $this->view->idCadastroXItem_cpf  = $campo->id_cadastro_item;
                    $this->view->rd_file_cpf  = $campo->chr_validacao;
                    $this->view->obs_file_cpf = $campo->vhr_observacao;
                }
                if($campo->id_item == 21){ //file_comprovante_residencia
                    //$this->view->file_comprovante_residencia = $campo->vhr_valor;
                    unset($arrRetorno);
                    unset($link);
                    unset($urlArquivo);
                    $arrRetorno = $this->montaLinkArquivo($cpf,'comprovante_residencia.pdf',$blnForum);
                    $urlArquivo = $arrRetorno['urlArquivo'];
                    $link       = $arrRetorno['linkAcesso'];
                    $this->view->file_comprovante_residencia = (file_exists($urlArquivo)) ? $link : "";
                    $this->view->arquivo_comprovante_residencia  = 'comprovante_residencia.pdf';//$nome_arquivo;
                    $this->view->idCadastroXItem_comprovante_residencia  = $campo->id_cadastro_item;
                    $this->view->rd_comprovante_residencia  = $campo->chr_validacao;
                    $this->view->obs_comprovante_residencia = $campo->vhr_observacao;
                }
                if($campo->id_item == 22){ //chk_veracidade
                    $this->view->chk_veracidade = $campo->vhr_valor;
                    $this->view->rd_veracidade  = $campo->chr_validacao;
                    $this->view->obs_veracidade = $campo->vhr_observacao;
                }
                if($campo->id_item == 23){ //chk_ciencia
                    $this->view->chk_ciencia = $campo->vhr_valor;
                    $this->view->rd_ciencia  = $campo->chr_validacao;
                    $this->view->obs_ciencia = $campo->vhr_observacao;
                }
                if($campo->id_item == 24){ //chk_conhecimento_plano
                    $this->view->chk_conhecimento_plano = $campo->vhr_valor;
                    $this->view->rd_conhecimento_plano  = $campo->chr_validacao;
                    $this->view->obs_conhecimento_plano = $campo->vhr_observacao;
                }
                if($campo->id_item == 25){ //chk_cargo_comissionado
                    $this->view->chk_cargo_comissionado = $campo->vhr_valor;
                    $this->view->rd_cargo_comissionado  = $campo->chr_validacao;
                    $this->view->obs_cargo_comissionado = $campo->vhr_observacao;
                }
                if($campo->id_item == 26){ //cargo_comissionado
                    $this->view->cargo_comissionado = $campo->vhr_valor;
                    $this->view->rd_comissionado  = $campo->chr_validacao;
                    $this->view->obs_comissionado = $campo->vhr_observacao;
                }
                if($campo->id_item == 27){ //file_comprovante_funcao_comissionado
                    //$this->view->file_comprovante_funcao_comissionado = $campo->vhr_valor;
                    unset($arrRetorno);
                    unset($link);
                    unset($urlArquivo);
                    $arrRetorno = $this->montaLinkArquivo($cpf,'comprovante_funcao_comissionado.pdf',$blnForum);
                    $urlArquivo = $arrRetorno['urlArquivo'];
                    $link       = $arrRetorno['linkAcesso'];
                    $this->view->file_comprovante_funcao_comissionado = (file_exists($urlArquivo)) ? $link : "";
                    $this->view->arquivo_comprovante_funcao_comissionado  = 'comprovante_funcao_comissionado.pdf';//$nome_arquivo;
                    $this->view->idCadastroXItem_comprovante_funcao_comissionado  = $campo->id_cadastro_item;
                    $this->view->rd_comprovante_funcao_comissionado  = $campo->chr_validacao;
                    $this->view->obs_comprovante_funcao_comissionado = $campo->vhr_observacao;
                }
                if($campo->id_item == 28){ //propostas
                    $this->view->propostas = $campo->vhr_valor;
                    $this->view->rd_proposta  = $campo->chr_validacao;
                    $this->view->obs_proposta = $campo->vhr_observacao;
                }
                if($campo->id_item == 29){ //file_curriculo
                    //$this->view->file_curriculo = $campo->vhr_valor;
                    unset($arrRetorno);
                    unset($link);
                    unset($urlArquivo);
                    $arrRetorno = $this->montaLinkArquivo($cpf,'curriculo.pdf',$blnForum);
                    $urlArquivo = $arrRetorno['urlArquivo'];
                    $link       = $arrRetorno['linkAcesso'];
                    $this->view->file_curriculo = (file_exists($urlArquivo)) ? $link : "";
                    $this->view->arquivo_curriculo  = 'curriculo.pdf'; //$nome_arquivo;
                    $this->view->idCadastroXItem_curriculo  = $campo->id_cadastro_item;
                    $this->view->rd_curriculo  = $campo->chr_validacao;
                    $this->view->obs_curriculo = $campo->vhr_observacao;
                }
                if($campo->id_item == 30){ //file_portfolio
                    //$this->view->file_portfolio = $campo->vhr_valor;
                    unset($arrRetorno);
                    unset($link);
                    unset($urlArquivo);
                    $arrRetorno = $this->montaLinkArquivo($cpf,'portfolio.pdf',$blnForum);
                    $urlArquivo = $arrRetorno['urlArquivo'];
                    $link       = $arrRetorno['linkAcesso'];
                    $this->view->file_portfolio = (file_exists($urlArquivo)) ? $link : "";
                    $this->view->arquivo_portfolio  = 'portfolio.pdf';//$nome_arquivo;
                    $this->view->idCadastroXItem_portfolio  = $campo->id_cadastro_item;
                    $this->view->rd_portfolio  = $campo->chr_validacao;
                    $this->view->obs_portfolio = $campo->vhr_observacao;
                }
                if($campo->id_item == 31){ //chk_candidato
                    $this->view->chk_candidato = $campo->vhr_valor;
                    $this->view->rd_chk_candidato  = $campo->chr_validacao;
                    $this->view->obs_chk_candidato = $campo->vhr_observacao;
                }
                if($campo->id_item == 32){ //file_carta_apoio
                    //$this->view->file_carta_apoio = $campo->vhr_valor;
                    unset($arrRetorno);
                    unset($link);
                    unset($urlArquivo);
                    $arrRetorno = $this->montaLinkArquivo($cpf,'carta_apoio.pdf',$blnForum);
                    $urlArquivo = $arrRetorno['urlArquivo'];
                    $link       = $arrRetorno['linkAcesso'];
                    $this->view->file_carta_apoio = (file_exists($urlArquivo)) ? $link : "";
                    $this->view->arquivo_carta_apoio  = 'carta_apoio.pdf'; //$nome_arquivo;
                    $this->view->idCadastroXItem_carta_apoio  = $campo->id_cadastro_item;
                    $this->view->rd_carta_apoio  = $campo->chr_validacao;
                    $this->view->obs_carta_apoio = $campo->vhr_observacao;
                }
                if($campo->id_item == 33){ //etnia
                    $this->view->etnia = $campo->vhr_valor;
                    $this->view->rd_etnia  = $campo->chr_validacao;
                    $this->view->obs_etnia = $campo->vhr_observacao;
                }

                $this->view->senha               = (!empty($campo->vhr_senha)) ? "senha" : null;
                $this->view->bol_cadastroenviado = $campo->bol_cadastroenviado;
                $this->view->int_tipocadastro    = $campo->int_tipocadastro;
                
                $this->view->bol_validacaocadastroeleitor = $campo->bol_validacaocadastroeleitor;
                $this->view->bol_validacaocadastrocandidato = $campo->bol_validacaocadastrocandidato;
                $this->view->bol_validacaofinal = $campo->bol_validacaofinal;
                
                $this->view->vhr_validacaocadastroeleitor = $campo->vhr_validacaocadastroeleitor;
                $this->view->vhr_validacaocadastrocandidato = $campo->vhr_validacaocadastrocandidato;
                $this->view->vhr_validacaofinal = $campo->vhr_validacaofinal;
                $this->view->idUsuarioCadastrado = $campo->id_usuario;
            }
        }
        
        public function setarValoresCamposViewRecurso($rsObj,$rsCadastro){
            
            $cpf = $rsCadastro[0]->vhr_login;
            $arrRetorno = array();
            foreach($rsObj as $chave => $campo){
                                
                $nome = $campo->vhr_nome_anexo;
                $caminho = $campo->vhr_caminho_anexo;
                
                $nomeArquivo = explode("/",$caminho);
                $nomeArquivo = $nomeArquivo[count($nomeArquivo)-1];
                
                $urlArquivo = $this->diretorioUpload.$cpf."/recurso/".$nomeArquivo;
                $hash = base64_encode($urlArquivo);
                
                $link = Zend_Controller_Front::getInstance()->getBaseUrl()."/viewblob/recurso/id/".$hash;
                
                if(file_exists($urlArquivo)){
                    $arrRetorno[$chave]['nomeArquivo'] = $nome;
                    $arrRetorno[$chave]['linkAcesso']  = $link;
                }
            }
            $this->view->dadosAnexoRecurso = $arrRetorno;
        }
        
        public function montaLinkArquivo($cpf, $nomeArquivo, $blnForum=false){
            $arrRetorno = array();
            
            $urlArquivo = $this->diretorioUpload.$cpf."/".$nomeArquivo;
            $arrRetorno['urlArquivo'] = $urlArquivo;
            $hash = base64_encode($urlArquivo);
            if($blnForum){
                $link = Zend_Controller_Front::getInstance()->getBaseUrl()."/viewblob/forum/id/".$hash;
            }else{
                $link = Zend_Controller_Front::getInstance()->getBaseUrl()."/viewblob/index/id/".$hash;
            }
            $arrRetorno['linkAcesso'] = $link;
            
            return $arrRetorno;
        }



        public function verificaHabilitacaoInscrito($idUsuario){

            $tbAvalicaoInscrito = new AvaliacaoFinalInscrito();
            $tbHabilitacaoSetorialUF = new Habilitarsetorialuf();

            $rsAvalicaoInscrito = $tbAvalicaoInscrito->buscar(array("id_usuario = ?"=>$idUsuario))->current();

            $msgSetorialUFNaoValidada = "Informamos que, apesar de sua inscrição ter sido validada, o seu estado não atingiu o quórum mínimo
                                        de cinco eleitores validados e por isso não haverá eleição de delegados estaduais deste Setorial,
                                        conforme §1º do artigo 21 da Portaria MinC nº/51/2012. <br />Agradecemos sua participação e comunicamos
                                        que seu nome e dados serão inseridos no cadastro cultural permanente do Ministério da Cultura.";

            $msgInscritoNaoValidado =  "Infelizmente não será possível a sua participação no processo eleitoral do CNPC porque sua inscrição
                                        não foi validada por não atender aos critérios estabelecidos pela Portaria MinC nº/51/2012.<br/>
                                        Informamos que após a conclusão do processo eleitoral, você poderá complementar seus dados para integrar
                                        o cadastro cultural permanente do Ministério da Cultura.";

            if(!empty($rsAvalicaoInscrito)){

                $idSetorial  = $rsAvalicaoInscrito->id_setorial;
                $uf          = $rsAvalicaoInscrito->chr_uf;

                if($rsAvalicaoInscrito->chr_avaliacao_eleitor == 1) //eleitor validado
                {
                    $arrBusca = array();
                    $arrBusca['id_setorial = ?']     = $idSetorial;
                    $arrBusca['chr_uf = ?']          = $uf;
                    $arrBusca['chr_habilitacao = ?'] = 1;
                    $rsHabilitacaoSetorialUF = $tbHabilitacaoSetorialUF->buscar($arrBusca)->current();

                    if(!empty($rsHabilitacaoSetorialUF)){
                        return "true";

                    }else{
                        $this->message($msgSetorialUFNaoValidada, "login/error", "ALERT");
                        return "false";
                    }
                }else{
                    //return "false";
                    $this->message($msgInscritoNaoValidado, "login/error", "ALERT");
                    return "false";
                }
            }else{
                //return "false";
                $this->message($msgInscritoNaoValidado, "login/error", "ALERT");
                return "false";
            }
        }

} // fecha class
