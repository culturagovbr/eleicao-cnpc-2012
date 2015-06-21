<?php 
/**
 * Controller Login
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class LoginController extends GenericController
{
	/**
	 * Reescreve o método init()
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		parent::init(); // chama o init() do pai GenericControllerNew
	} // fecha método init()



	/**
	 * Método com o formulário de login
	 * @access public
	 * @param void
	 * @return void
	 */
	public function indexAction() {} // fecha método indexAction()



	/**
	 * Método que efetua o login
	 * @access public
	 * @param void
	 * @return void
	 */
	public function loginAction() {
            $PasswordWP = new PasswordHash(8, false);

            // recebe os dados do formulario via post
            $post = Zend_Registry::get('post');
            $username = retiraMascara($post->vhr_login); // recebe o login sem mï¿½scaras
            $password = $post->vhr_senha; // recebe a senha
			
			if(!$this->getRequest()->isPost()){$this->_redirect('login/index');}

            try {
                // valida os dados
                if (empty($username) || empty($password)) { // verifica se os campos foram preenchidos
                    parent::message("Senha ou login inv&aacute;lidos", "/login/index", "ALERT");
                } else if (strlen($username) < 11 || !Validacao::validarCPF($username)) { // verifica se o CPF invalido
                    parent::message("CPF inv&aacute;lido", "/login/index", "ALERT");
                } else {

                    //AUTENTICA USUARIO
                    $tblUsuario = new Usuario();
                    //Verifica a existencia do username
                    $rsUser = $tblUsuario->buscar(array('vhr_login = ?' => $username));
                    if (count($rsUser) > 0) {
                        $rsUser = $rsUser->current();
                        //Verifica se a senha digitada confere com a senha cadastrada
//                        xd($PasswordWP->CheckPassword($password, $rsUser['vhr_senha']));
                        if (!$PasswordWP->CheckPassword($password, $rsUser['vhr_senha'])) {
                            parent::message("Usu&aacute;rio n&atilde;o encontrado! Verifique o login e a senha digitada.", "/login/index", "ALERT");
                        }
                    }

                    $retorno = $tblUsuario->login($username, $rsUser['vhr_senha']);
                    $auth = Zend_Auth::getInstance();

                    if (!$retorno && !$auth->hasIdentity()) {
                        parent::message("Usu&aacute;rio n&atilde;o encontrado! Verifique o login e a senha digitada.", "/login/index", "ALERT");
                    } else {

                        $idPerfil = $auth->getIdentity()->id_perfil;
                        $idUsuario = $auth->getIdentity()->id_usuario;

                        if ($idPerfil == 1) { //login de Inscrito
                            //$this->_redirect("inscricao/form-inscricao/usu/{$idUsuario}");
                            //$this->_redirect("recurso/form-recurso/usu/{$idUsuario}");
                            $retorno = $this->verificaHabilitacaoInscrito($idUsuario);
                            //$retorno = "true";
                            if($retorno == "true"){
                                if(date('YmdHis') >= '20121018000000' && date('YmdHis') < '20121023235959'): //real
                                //if(date('YmdHis') >= '20121017000000' && date('YmdHis') < '20121017163500'): //teste
                                    $this->_redirect("votacao/");
                                else:
                                    $this->_redirect("forum/");
                                endif;
                            }else{
                                //parent::message($msgInscritoNaoValidado, "login/error", "ALERT");
                                $auth->clearIdentity(); // limpa a autenticao
                                unset($_SESSION);
                                $this->_redirect("login/error/?msg=02&type=ALERT");
                            }
                            die;
                        }
                        if ($idPerfil == 2 || $idPerfil == 3 || $idPerfil == 4 || $idPerfil == 5) { //login de Inscrito
                            $this->_redirect("admin/index/");
                            die;
                        }
                    }

                    //return $this->_helper->redirector->goToRoute(array('controller' => 'principalproponente'), null, true);
                } // fecha else
            } // fecha try
            catch (Exception $e) {
                parent::message($e->getMessage(), "index", "ERROR");
            }
        }

// fecha loginAction

        public function recuperarSenhaAction() {
//            xd('aqui');
        }

        
        public function recuperarSenhaValidacaoAction() {
            $where = array();
            $where['u.vhr_login = ?'] = retiraMascara($_POST['cpf']);
            $where['u.vhr_email = ?'] = $_POST['email'];
            //$where['ci.vhr_valor = ?'] = retiraMascara($_POST['dtNascimento']);
            $tblUsuario = new Usuario();
            $buscarDados = $tblUsuario->confirmarDadosRecuperarSenha($where);
			
            if(!empty($buscarDados)){
                $nome = $buscarDados->vhr_nome;
                $login = $buscarDados->vhr_login;

                $senhaGerada = $this->gerarSenha(8);
                
                $whereAlterar = array();
                $whereAlterar['vhr_login = ?'] = retiraMascara($_POST['cpf']);
                
                $PasswordHash = new PasswordHash(8,false);
                $dadosAlterar = array(
                        'vhr_senha' => $PasswordHash->HashPassword($senhaGerada)
                    );
                $buscarDados = $tblUsuario->alterar($dadosAlterar, $whereAlterar);
                
				$config = array (
					'port' => '25'
				);
				$smtp = "smtp.cultura.gov.br";			
				$mailTransport = new Zend_Mail_Transport_Smtp($smtp, $config);
				$mail = new Zend_Mail();
				$mail->setFrom('inscricoescnpc@cultura.gov.br', 'Inscrições CNPC');
				$mail->addTo($_POST['email']);
				
				$msgEmail = "Olá <b>".$nome."</b>, <br><br>
						    A sua senha para acessar o sítio do Processo Eleitoral do CNPC foi recuperada, seus dados para acesso são:<br><br>
							login: <b>".$login."</b> <br>
							senha: <b>".$senhaGerada."</b>";

				$mail->setBodyHtml($msgEmail);
				$mail->setSubject('Senha de acesso [Processo Eleitorado do CNPC]');
				$mail->send($mailTransport);
				
                parent::message("Uma nova senha foi enviada ao seu email.", "login/index", "CONFIRM");
                
            } else {
                parent::message("Dados não conferem. Favor entrar em contato com o Suporte para solicitar a geração de uma nova senha. <br>E-mail do suporte: cnpccomunicacao@cultura.gov.br.", "login/recuperar-senha", "ALERT");
            }

        }
        
        function gerarSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false) {
            $lmin = 'abcdefghijklmnopqrstuvwxyz';
            $lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $num = '1234567890';
            $simb = '!@#$%*-';
            $retorno = '';
            $caracteres = '';

            $caracteres .= $lmin;
            if ($maiusculas)
                $caracteres .= $lmai;
            if ($numeros)
                $caracteres .= $num;
            if ($simbolos)
                $caracteres .= $simb;

            $len = strlen($caracteres);
            for ($n = 1; $n <= $tamanho; $n++) {
                $rand = mt_rand(1, $len);
                $retorno .= $caracteres[$rand - 1];
            }
            return $retorno;
        }

        public function errorAction(){
            
        }
}
?>
