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
                            $this->_redirect("inscricao/form-inscricao/usu/{$idUsuario}");
                            die;
                        }
                        if ($idPerfil == 2 || $idPerfil == 3 || $idPerfil == 4) { //login de Inscrito
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


	public function cadastrarusuarioAction()
	{
		if ( $_POST )
		{
			$post     = Zend_Registry::get('post');
			$cpf = Mascara::delMaskCNPJ(Mascara::delMaskCPF($post->cpf)); // recebe cpf
			$nome = $post->nome; // recebe o nome
			$dataNasc = $post->dataNasc; // recebe dataNasc
			$email = $post->email; // recebe email
			$emailConf = $post->emailConf; // recebe confirmacao senha

			if ( trim($email) != trim($emailConf) )
			{
				parent::message("Digite o email certo!", "/login/cadastrarusuario", "ALERT");
			}

			$gerarSenha = Gerarsenha::gerasenha(15, true, true, true, true);

			$encriptaSenha = EncriptaSenhaDAO::encriptaSenha($cpf, $gerarSenha);
			$SenhaFinal = $encriptaSenha[0]->senha;

			$dataFinal = data::dataAmericana($dataNasc);

			$dados = array(
                    "Cpf" => $cpf,
                    "Nome" => $nome,
                    "DtNascimento" => $dataFinal,
                    "Email" => $email,
                    "Senha" => $SenhaFinal,
                    "DtCadastro" => date("Y-m-d"),
                    "Situacao" => 1,
                    "DtSituacao" => date("Y-m-d")
			);


			$sgcAcesso = new Sgcacesso();
			$sgcAcessoBuscaCpf = $sgcAcesso->buscar(array("Cpf = ?" => $cpf));
			$sgcAcessoBuscaCpfArray = $sgcAcessoBuscaCpf->toArray();

			if ( !empty ( $sgcAcessoBuscaCpfArray ))
			{
				parent::message("CPF j&aacute; cadastrado", "/login/cadastrarusuario", "ALERT");
			}

			$sgcAcessoBuscaEmail = $sgcAcesso->buscar(array("Email = ?" => $email));
			$sgcAcessoBuscaEmailArray = $sgcAcessoBuscaEmail->toArray();

			if ( !empty ( $sgcAcessoBuscaEmailArray ))
			{
				parent::message("E-mail j&aacute; cadastrado", "/login/cadastrarusuario", "ALERT");
			}

			if ( empty ( $sgcAcessoBuscaCpfArray ) && empty ( $sgcAcessoBuscaEmailArray ) )
			{
				$sgcAcessoSave = $sgcAcesso->salvar($dados);


				/**
				 * ==============================================================
				 * INICIO DO VINCULO DO RESPONSAVEL COM ELE MESMO (PROPONENTE)
				 * ==============================================================
				 */

				/* ========== VERIFICA SE O RESPONSAVEL JA TEM CADASTRO COMO PROPONENTE ========== */
				$Agentes = new Agentes();
				$Visao   = new Visao();
				$buscarAgente = $Agentes->buscar(array('CNPJCPF = ?' => $cpf));
				$idAgenteProp = count($buscarAgente) > 0 ? $buscarAgente[0]->idAgente : 0;
				$buscarVisao  = $Visao->buscar(array('Visao = ?' => 144, 'stAtivo = ?' => 'A', 'idAgente = ?' => $idAgenteProp));

				/* ========== VINCULA O RESPONSAVEL A SEU PROPRIO PERFIL DE PROPONENTE ========== */
				if ( count($buscarVisao) > 0 ) :
				$tbVinculo    = new TbVinculo();
				$idResp = $sgcAcesso->buscar(array('Cpf = ?' => $sgcAcessoSave)); // pega o id do responsável cadastrado

				$dadosVinculo = array(
						'idAgenteProponente'    => $idAgenteProp
				,'dtVinculo'            => new Zend_Db_Expr('GETDATE()')
				,'siVinculo'            => 2
				,'idUsuarioResponsavel' => $idResp[0]->IdUsuario);
				$tbVinculo->inserir($dadosVinculo);
				endif;

				/**
				 * ==============================================================
				 * FIM DO VINCULO DO RESPONSAVEL COM ELE MESMO (PROPONENTE)
				 * ==============================================================
				 */


				/* ========== ENVIA O E-MAIL PARA O USUARIO ========== */
				$assunto = "Cadastro SALICWEB";
				$perfil = 'SALICWEB';
				$mens  = "Ol&aacute; $nome ,<br><br>";
				$mens .= "Senha....: $SenhaFinal <br><br>";
				$mens .= "Esta &eacute; a sua senha de acesso ao Sistema de Apresentaç?o de Projetos via Web do ";
				$mens .= "Minist&eacute;rio da Cultura.<br><br>Lembramos que a mesma dever&aacute; ser ";
				$mens .= "trocada no seu primeiro acesso ao sistema.<br><br>";
				$mens .= "Esta &eacute; uma mensagem autom&aacute;tica. Por favor n?o responda.<br><br>";
				$mens .= "Atenciosamente,<br>Minist&eacute;rio da Cultura";

				$enviaEmail = EmailDAO::enviarEmail($email, $assunto, $mens, $perfil);
				parent::message("Cadastro efetuado com sucesso. Verifique a senha no seu email", "/login/index", "CONFIRM");
			}
		} // fecha if
	} // fecha método cadastrarusuarioAction()

	public function solicitarsenhaAction()
	{

		if ( $_POST )
		{
			//$enviaEmail = EnviaemailController::enviaEmail("ewrwr", "tiago.rodrigues@cultura.gov.br", "tisomar@gmail.com");
			$post     = Zend_Registry::get('post');
			$cpf = Mascara::delMaskCNPJ(Mascara::delMaskCPF($post->cpf)); // recebe cpf
			$dataNasc = data::dataAmericana($post->dataNasc); // recebe dataNasc
			$email = $post->email; // recebe email

			$sgcAcesso = new Sgcacesso();
			$sgcAcessoBuscaCpf = $sgcAcesso->buscar(array("Cpf = ?" => $cpf, "Email = ?" => $email, "DtNascimento = ?" => $dataNasc));
			$verificaUsuario = $sgcAcessoBuscaCpf->toArray();
			if ( empty ( $verificaUsuario ) )
			{
				parent::message("Usu&aacute;rio n&atilde;o cadastrado!", "/login/index", "ALERT");
			}

			$sgcAcessoBuscaCpfArray = $sgcAcessoBuscaCpf->toArray();
			$nome = $sgcAcessoBuscaCpfArray[0]['Nome'];
			$senha = Gerarsenha::gerasenha(15, true, true, true, true);
			$senhaFormatada = str_replace(">", "", str_replace("<", "", str_replace("'","", $senha)));

			$dados = array(
                        "IdUsuario" => $sgcAcessoBuscaCpfArray[0]['IdUsuario'],
                        "Senha" => $senhaFormatada,
                        "Situacao" => 1,
                        "DtSituacao" => date("Y-m-d")
			);
			$sgcAcessoSave = $sgcAcesso->salvar($dados);

			$assunto = "Cadastro SALICWEB";
			$perfil = "SALICWEB";
			$mens  = "Ol&aacute; " . $nome . ",<br><br>";
			$mens .= "Senha....: " . $senhaFormatada . "<br><br>";
			$mens .= "Esta &eacute; a sua senha tempor&aacute;ria de acesso ao Sistema de Apresenta&ccedil;&atilde;o de Projetos via Web do ";
			$mens .= "Minist&eacute;rio da Cultura.<br><br>Lembramos que a mesma dever&aacute; ser ";
			$mens .= "trocada no seu primeiro acesso ao sistema.<br><br>";
			$mens .= "Esta &eacute; uma mensagem autom&aacute;tica. Por favor n?o responda.<br><br>";
			$mens .= "Atenciosamente,<br>Minist&eacute;rio da Cultura";

			$enviaEmail = EmailDAO::enviarEmail($email, $assunto, $mens, $perfil);
			parent::message("Senha gerada com sucesso. Verifique seu email!", "/login/index", "CONFIRM");
		}
	}
	
	public function alterarsenhaAction()
	{
		// autenticação proponente (Novo Salic)		

		/* ========== INÍCIO ID DO USUÁRIO LOGADO ========== */
		$auth    = Zend_Auth::getInstance(); // pega a autenticação
		$Usuario = new Usuario();


		// verifica se o usuário logado é agente
		$idUsuario = $Usuario->getIdUsuario(null, $auth->getIdentity()->Cpf);
		if ( $idUsuario ){
			// caso não tenha idAgente, atribui o idUsuario
			$this->getIdUsuario = ($idUsuario) ? $idUsuario['idAgente'] : $auth->getIdentity()->IdUsuario;
			$this->getIdUsuario = empty($this->getIdUsuario) ? 0 : $this->getIdUsuario;
			/* ========== FIM ID DO USUÁRIO LOGADO ========== */
			parent::perfil(4);
		}

		Zend_Layout::startMvc(array('layout' => 'layout_proponente'));

		$this->view->cpf = "";
		$this->view->nome = "";
		$dataFormatada = "";
		$this->view->dtNascimento = "";
		$this->view->email = "";

		if ( count(Zend_Auth::getInstance()->getIdentity()) > 0 )
		{
			$auth = Zend_Auth::getInstance();// instancia da autenticação

			$idUsuario = $auth->getIdentity()->IdUsuario;
			$this->view->idUsuario = $auth->getIdentity()->IdUsuario;
			$cpf = $auth->getIdentity()->Cpf;
			$this->view->cpf = $auth->getIdentity()->Cpf;
			$this->view->nome = $auth->getIdentity()->Nome;
			$dataFormatada = data::formatarDataMssql($auth->getIdentity()->DtNascimento);
			$this->view->dtNascimento = $dataFormatada;
			$this->view->email = $auth->getIdentity()->Email;

		}

		if ( $_POST ) {

			$post     = Zend_Registry::get('post');
			$senhaAtual = $post->senhaAtual; // recebe senha atua
			$senhaNova = $post->senhaNova; // recebe senha nova
			$repeteSenha = $post->repeteSenha; // recebe repete senha
				
			$senhaAtual = str_replace("##menor##", "<", $senhaAtual);
			$senhaAtual = str_replace("##maior##", ">", $senhaAtual);
			$senhaAtual = str_replace("##aspa##", "'", $senhaAtual);

			$sgcAcesso = new Sgcacesso();

			if ( empty ($_POST['idUsuario']) )
			{
				$idUsuario = $_POST['idUsuarioGet'];
				$buscarSenha  = $sgcAcesso->buscar(array('IdUsuario = ?' => $idUsuario))->toArray();
			}
			else
			{
				$idUsuario = $_POST['idUsuario'];
				$buscarSenha  = $sgcAcesso->buscar(array('IdUsuario = ?' => $idUsuario))->toArray();
			}
			$senhaAtualBanco = $buscarSenha[0]['Senha'];


			if ( empty ( $cpf ) )
			{
				$cpf = $buscarSenha[0]['Cpf'];
			}

			// busca a senha do banco TABELAS
			$Usuarios     = new Usuario();
			$buscarCPF    = $Usuarios->buscar(array('usu_identificacao = ?' => trim($cpf)));
			$cpfTabelas   = count($buscarCPF) > 0 ? true : false;
			$senhaTabelas = $Usuarios->verificarSenha(trim($cpf) , $senhaAtual);

			if ( $buscarSenha[0]['Situacao'] != 1)
			{

				$comparaSenha = EncriptaSenhaDAO::encriptaSenha($cpf, $senhaAtual);
				$SenhaFinal = $comparaSenha[0]->senha;


				if ( trim($senhaAtualBanco) !=  trim($SenhaFinal) && ($cpfTabelas && !$senhaTabelas) )
				{
					parent::message("Por favor, digite a senha atual correta!", "/login/alterarsenha?idUsuario=$idUsuario","ALERT");
				}
			}

			else
			{
				if ( trim($senhaAtualBanco) !=  trim($senhaAtual) && ($cpfTabelas && !$senhaTabelas) )
				{
					parent::message("Por favor, digite a senha atual correta!", "/login/alterarsenha?idUsuario=$idUsuario","ALERT");
				}
			}

			if ( trim($senhaNova) == trim($repeteSenha) && !empty( $senhaNova ) && !empty( $repeteSenha ))
			{

				if ( empty ( $idUsuario ) )
				{
					$post     = Zend_Registry::get('post');
					$idUsuario = $post->idUsuario;
				}
				$sgcAcessoBuscaCpf = $sgcAcesso->buscar(array("IdUsuario = ?" => $idUsuario));
				$cpf = $sgcAcessoBuscaCpf[0]['Cpf'];
				$nome = $sgcAcessoBuscaCpf[0]['Nome'];
				$email = $sgcAcessoBuscaCpf[0]['Email'];

				$encriptaSenha = EncriptaSenhaDAO::encriptaSenha($cpf, $senhaNova);
				$SenhaFinal = $encriptaSenha[0]->senha;

				$dados = array(
                            "IdUsuario" => $idUsuario,
                            "Senha" => $SenhaFinal,
                            "Situacao" => 3,
                            "DtSituacao" => date("Y-m-d")
				);
				$sgcAcessoSave = $sgcAcesso->salvar($dados);


				$assunto = "Cadastro SALICWEB";
				$perfil = "SALICWEB";
				$mens  = "Ol&aacute; " . $nome . ",<br><br>";
				$mens .= "Senha....: " . $senhaNova . "<br><br>";
				$mens .= "Esta &eacute; a sua nova senha de acesso ao Sistema de Apresenta&ccedil;&atilde;o de Projetos via Web do ";
				$mens .= "Minist&eacute;rio da Cultura.<br><br>Lembramos que a mesma dever&aacute; ser ";
				$mens .= "trocada no seu primeiro acesso ao sistema.<br><br>";
				$mens .= "Esta &eacute; uma mensagem autom&aacute;tica. Por favor n?o responda.<br><br>";
				$mens .= "Atenciosamente,<br>Minist&eacute;rio da Cultura";

				//$enviaEmail = EmailDAO::enviarEmail($email, $assunto, $mens, $perfil);
				parent::message("Senha alterada com sucesso!", "/login/index", "CONFIRM");
			}
		}
	}
	
	public function alterarsenhausuarioAction()
	{
		parent::perfil(0);
		// autenticação proponente (Novo Salic)

		/* ========== INÍCIO ID DO USUÁRIO LOGADO ========== */
		$auth    = Zend_Auth::getInstance(); // pega a autenticação
		$Usuario = new Usuario();

		// verifica se o usuário logado é agente
		$idUsuario = $Usuario->getIdUsuario(null, $auth->getIdentity()->usu_identificacao);
		if ( isset($auth->getIdentity()->usu_identificacao) ){
			//xd($auth->getIdentity());
			// caso não tenha idAgente, atribui o idUsuario
			$this->getIdUsuario = ($idUsuario) ? $idUsuario['idAgente'] : $auth->getIdentity()->usu_codigo;
			//$this->getIdUsuario = empty($this->getIdUsuario) ? 0 : $this->getIdUsuario;
			/* ========== FIM ID DO USUÁRIO LOGADO ========== */
			
		}

		Zend_Layout::startMvc(array('layout' => 'layout'));

		$this->view->cpf  = "";
		$this->view->nome = "";

		if ( count(Zend_Auth::getInstance()->getIdentity()) > 0 )
		{
			$auth = Zend_Auth::getInstance();// instancia da autenticação

			$idUsuario = $auth->getIdentity()->usu_codigo;			
			$cpf       = $auth->getIdentity()->usu_identificacao;
			
			$this->view->idUsuario = $auth->getIdentity()->usu_codigo;
			$this->view->cpf       = $auth->getIdentity()->usu_identificacao;
			$this->view->nome      = $auth->getIdentity()->usu_nome;

		}

		if ( $_POST ) {

			$post = Zend_Registry::get('post');

			$senhaAtual  = $post->senhaAtual; // recebe senha atua
			$senhaNova   = $post->senhaNova; // recebe senha nova
			$repeteSenha = $post->repeteSenha; // recebe repete senha
				
			$senhaAtual = str_replace("##menor##", "<", $senhaAtual);
			$senhaAtual = str_replace("##maior##", ">", $senhaAtual);
			$senhaAtual = str_replace("##aspa##", "'", $senhaAtual);
			
			if ( empty ($_POST['idUsuario']) )
			{
				$idUsuario = $_POST['idUsuarioGet'];
				$buscarSenha  = $Usuario->buscar(array('usu_codigo = ?' => $idUsuario))->toArray();
			}
			else
			{
				$idUsuario = $_POST['idUsuario'];
				$buscarSenha  = $Usuario->buscar(array('usu_codigo = ?' => $idUsuario))->toArray();
			}			
			$senhaAtualBanco = $buscarSenha[0]['usu_senha'];
			
			$comparaSenha = EncriptaSenhaDAO::encriptaSenha($cpf, $senhaAtual);
			$SenhaFinal = $comparaSenha[0]->senha;

			$comparaSenhaNova = EncriptaSenhaDAO::encriptaSenha($cpf, $senhaNova);
			$senhaNovaCript = $comparaSenhaNova[0]->senha;

			
			if ( trim($senhaAtualBanco) !=  trim($SenhaFinal)){
				parent::message("Por favor, digite a senha atual correta!", "/login/alterarsenhausuario?idUsuario=$idUsuario","ALERT");
			}
			
			if($repeteSenha != $senhaNova){
				parent::message("Senhas diferentes!", "/login/alterarsenhausuario?idUsuario=$idUsuario","ALERT");
			}
			
			if ( $senhaAtualBanco ==  $senhaNovaCript){
				parent::message("Por favor, digite a senha diferente da atual!", "/login/alterarsenhausuario?idUsuario=$idUsuario","ALERT");
			}
			
			if ( strlen(trim($senhaNova))<5){
				parent::message("Por favor, sua nova senha deverá conter no mínimo 5 dígitos!", "/login/alterarsenhausuario?idUsuario=$idUsuario","ALERT");
			}
			
			$alterar = $Usuario->alterarSenha($cpf, $senhaNova);
			if($alterar){
				parent::message("Senha alterada com sucesso!", "/principal/index/","CONFIRM");
			}else{
				parent::message("Erro ao alterar senha!", "/login/alterarsenhausuario?idUsuario=$idUsuario","ALERT");
			}		
		}
	}

	public function logarcomoAction()
	{

		$this->_helper->layout->disableLayout(); // desabilita Zend_Layout
		Zend_Layout::startMvc(array('layout' => 'layout_proponente'));

		$buscaUsuario = new Usuariosorgaosgrupos();
		$buscaUsuarioRs = $buscaUsuario->buscarUsuariosOrgaosGrupos(
		array ('gru_status > ?' => 0, 'sis_codigo = ?' => 21), array( 'usu_nome asc' ));

		$this->view->buscaUsuario = $buscaUsuarioRs->toArray();


		if ($_POST)
		{


			// recebe os dados do formul&aacute;rio via post
			$post     = Zend_Registry::get('post');
			$username = Mascara::delMaskCNPJ(Mascara::delMaskCPF($post->cpf)); // recebe o login sem mï¿½scaras
			$password = $post->senha; // recebe a senha
			$idLogarComo =  $post->logarComo;


			$sql = "SELECT tabelas.dbo.fnEncriptaSenha('" . $username . "', '" . $password . "') as senha";
			$db = Zend_Registry::get('db');
			$db->setFetchMode(Zend_DB::FETCH_OBJ);
			$senha =  $db->fetchRow($sql);

			$SenhaFinal = $senha->senha;

			$usuario = new Usuario();
			$usuarioRs = $usuario->buscar(
			array('usu_identificacao = ?' => $username, 'usu_senha = ?'=> $SenhaFinal ));

			if ( !empty ( $usuarioRs ) )
			{
				$usuarioRs = $usuario->buscar(
				array('usu_identificacao = ?' => $idLogarComo ))->current();
				$senha = $usuarioRs->usu_senha;

				$Usuario = new Usuario();
				$buscar = $Usuario->loginSemCript($idLogarComo, $senha);

				if ($buscar) // acesso permitido
				{
					$auth = Zend_Auth::getInstance(); // instancia da autenticaç?o

					// registra o primeiro grupo do usu&aacute;rio (pega unidade autorizada, organiza e grupo do usuaàio)
					$Grupo   = $Usuario->buscarUnidades($auth->getIdentity()->usu_codigo, 21); // busca todos os grupos do usuï¿½rio

					$GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo'); // cria a sess?o com o grupo ativo
					$GrupoAtivo->codGrupo = $Grupo[0]->gru_codigo; // armazena o grupo na sess?o
					$GrupoAtivo->codOrgao = $Grupo[0]->uog_orgao; // armazena o org?o na sess?o

					// redireciona para o Controller protegido
					return $this->_helper->redirector->goToRoute(array('controller' => 'principal'), null, true);
				} // fecha if
			}
		}
	}

	public function alterardadosAction()
	{
		// autenticação proponente (Novo Salic)
		parent::perfil(4);

		/* ========== INÍCIO ID DO USUÁRIO LOGADO ========== */
		$auth    = Zend_Auth::getInstance(); // pega a autenticação
		$Usuario = new Usuario();

		// verifica se o usuário logado é agente
		$idUsuario = $Usuario->getIdUsuario(null, $auth->getIdentity()->Cpf);

		// caso não tenha idAgente, atribui o idUsuario
		$this->getIdUsuario = ($idUsuario) ? $idUsuario['idAgente'] : $auth->getIdentity()->IdUsuario;
		$this->getIdUsuario = empty($this->getIdUsuario) ? 0 : $this->getIdUsuario;
		/* ========== FIM ID DO USUÁRIO LOGADO ========== */

		$sgcAcesso = new Sgcacesso();
		$auth = Zend_Auth::getInstance();// instancia da autenticação
		$cpf = Mascara::delMaskCPF($auth->getIdentity()->Cpf);
		$buscarDados =  $sgcAcesso->buscar(array ('Cpf = ?' => $cpf))->current();


		if ( count(Zend_Auth::getInstance()->getIdentity()) > 0 )
		{
			$auth = Zend_Auth::getInstance();// instancia da autenticação
			if  ( strlen($buscarDados['Cpf']) > 11  )
			{
				$this->view->cpf = Mascara::addMaskCNPJ($buscarDados['Cpf']);
			}
			else
			{
				$this->view->cpf = Mascara::addMaskCPF($buscarDados['Cpf']);
			}

			$this->view->nome = $buscarDados['Nome'];
			//$dataFormatada = data::formatarDataMssql($buscarDados['DtNascimento']);
			$dataFormatada = ConverteData($buscarDados['DtNascimento'],5);
			$this->view->dtNascimento = $dataFormatada;
			$this->view->email = $buscarDados['Email'];

		}

		$this->_helper->layout->disableLayout(); // desabilita Zend_Layout
		Zend_Layout::startMvc(array('layout' => 'layout_proponente'));


		if ( $_POST )
		{


			$post     = Zend_Registry::get('post');
			$cpf = Mascara::delMaskCNPJ(Mascara::delMaskCPF($post->cpf)); // recebe cpf
			$nome = $post->nome; // recebe o nome
			$dataNasc = $post->dataNasc; // recebe dataNasc
			$email = $post->email; // recebe email
			$emailConf = $post->emailConf; // recebe confirmacao senha



			if ( trim($email) != trim($emailConf) )
			{
				parent::message("Digite o email certo!", "/login/alterardados", "ALERT");
			}



			$dataFinal = data::dataAmericana($dataNasc);

			$dados = array(
                    "IdUsuario" => $auth->getIdentity()->IdUsuario,
                    "Cpf" => $cpf,
                    "Nome" => $nome,
                    "DtNascimento" => $dataFinal,
                    "Email" => $email,
                    "DtCadastro" => date("Y-m-d"),
                    "DtSituacao" => date("Y-m-d")
                );



                $sgcAcessoSave = $sgcAcesso->salvar($dados);

                parent::message("Dados alterados com sucesso", "principalproponente", "CONFIRM");
            }
        }
        
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
}
?>
