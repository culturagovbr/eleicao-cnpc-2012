<?php
/**
 * Login e autenticao
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class UsuarioController extends GenericController
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
			return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }
        parent::init(); // chama o init() do pai GenericControllerNew
        
    }
    
    public function indexAction()
    {
        
    }
    
    public function listarCadastrosAction() {
        
        //perfil minico que pode acessa essa pagina
        parent::perfilMinimo(4); //perfil adminsitrador
            
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
        //$arrBusca["bol_cadastroenviado = ?"] = 1;
        if(!empty($_POST['cpf'])){
            $arrBusca["vhr_login = ?"] = retiraMascara($_POST['cpf']);
        }
        if(!empty($_POST['nome'])){
            $nome = utf8_decode($_POST['nome']);
            $arrBusca["vhr_nome like '%{$nome}%'"] = '?';
        }
		if(!empty($_POST['perfil'])){
            $arrBusca["id_perfil = ?"] = $_POST['perfil'];
        }
        
        $tblUsuarios = new Usuario();
        $total = 0;
        $total = $tblUsuarios->buscaUsuarios($arrBusca, array(), null, null, true);

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) $fim = $total;

        $ordem = array();
        if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }else{$ordem = array('3 ASC');}
        $rs = $tblUsuarios->buscaUsuarios($arrBusca, $ordem, $tamanho, $inicio);

        $this->view->registros 	  = $rs;
        $this->view->pag 	  = $pag;
        $this->view->total 	  = $total;
        $this->view->inicio 	  = ($inicio+1);
        $this->view->fim 	  = $fim;
        $this->view->totalPag     = $totalPag;
        $this->view->intTamPag    = $this->intTamPag;
        $this->view->parametrosBusca= $_POST;
    }
    
    public function editarAction()
    {
        //perfil minico que pode acessa essa pagina
        parent::perfilMinimo(4); //perfil adminsitrador
        
        $tblSetorial = new Setorial();
        $Setoriais = $tblSetorial->buscar();
        
        $where['vhr_login = ?'] = $_POST['EditUserCpf'];
        $tblUsuario = new Usuario();
        $rs = $tblUsuario->buscar($where);
        if(count($rs)>0){
            $rs = $rs->current();
        }
        $this->view->dadosUsuario = $rs;
        $this->view->setoriaisCombo = $Setoriais;
        
        $setoriaisUsuario = array();
        $idSetoriaisUsu = array();
        $tblUsuario = new UsuarioXSetorial();
        $setoriaisUsuario = $tblUsuario->buscarSetoriaisPorCpf(array('u.vhr_login = ?' => $_POST['EditUserCpf']));
        if(count($setoriaisUsuario)>0){
            foreach ($setoriaisUsuario as $value) {
                $ids[] = $value['id_setorial'];
            }
            $setoriaisUsuario = $ids;
        }
        $this->view->setoriaisUsuario = $setoriaisUsuario;
    }
    
    public function alterarAction()
    {
        //perfil minico que pode acessa essa pagina
        parent::perfilMinimo(4); //perfil adminsitrador
        
        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        
        $cpf = retiraMascara($_POST['vhr_login']);
        $validaCPF = Validacao::validarCPF($cpf);
        
        if(!$validaCPF && $cpf != "00000000000"){
            parent::message("CPF inv&aacute;lido", "usuario/", "ERROR");
        }
		        
        $email = Validacao::validarEmail($_POST['vhr_email']);
        //se for email inválido, retorna msg de erro.
        if(!$email){
            parent::message("Email inv&aacute;lido", "usuario/", "ERROR");
        }
        
        try{

            $db->beginTransaction();
            
            $PasswordWP = new PasswordHash(8,false);
            
            //VERIFICA SE O USUARIO JA EXISTE
            $tblUsuario = new Usuario();
            $cpf = retiraMascara($_POST['vhr_login']);
            $rsUsuario = $tblUsuario->buscar(array('vhr_login = ?' => $cpf));
            
            if(count($rsUsuario)>0){
                $rsUsuario = $rsUsuario->current();
                $dadosUsuario = array(
                                "vhr_nome"  => $_POST['vhr_nome'],
                                "vhr_email"  => $_POST['vhr_email']
                                );
                if($rsUsuario->id_perfil > 1){
                    $dadosUsuario['id_perfil'] = $_POST['id_perfil'];
                }
                if(!empty($_POST['vhr_senha'])){
                    $dadosUsuario['vhr_senha'] = $PasswordWP->HashPassword($_POST['vhr_senha']);
                }
                $where = array();
                $where['id_usuario = ?'] = $rsUsuario->id_usuario;
                $tblUsuario->alterar($dadosUsuario, $where);
                
                $idUser = $rsUsuario['id_usuario'];
                
                if(isset($_POST['id_setorial']) && count($_POST['id_setorial']) > 0){

                    //deleta os setoriais atuais
                    $tblUsuarioXSetorial = new UsuarioXSetorial();
                    $deletarSetoriaisAtuais = $tblUsuarioXSetorial->delete('id_usuario = '.$idUser);
                    
                    //insere novamente os setoriais selecionados
                    foreach ($_POST['id_setorial'] as $idSetorial) {
                        $dadosSetoriais = array(
                                "id_usuario"  => $idUser,
                                "id_setorial"  => $idSetorial
                                );
                        $tblUsuarioXSetorial->inserir($dadosSetoriais);
                    }
                } else {
                    $db->rollBack();
                    $this->_helper->viewRenderer->setNoRender(true);
                    $this->_helper->flashMessenger->addMessage("Selecione no mínimo um setorial para o usuário.");
                    $this->_helper->flashMessengerType->addMessage("ERROR");
                    $this->_redirect("usuario/index/");
                }
                
                $msg = 'Usu&aacute;rio alterado com sucesso!';
                $type = 'CONFIRM';
            } else {
                $msg = 'CPF n&aatilde;o encontrado.';
                $type = 'ALERT';
            }
            
            
            if(!empty($_POST['vhr_senha'])){
			
                $config = array (
					'port' => '25'
                );
				$smtp = "smtp.cultura.gov.br";
                $mailTransport = new Zend_Mail_Transport_Smtp($smtp, $config);
                $mail = new Zend_Mail();
				$mail->setFrom('inscricoescnpc@cultura.gov.br', 'Inscrições CNPC');
                $mail->addTo($_POST['vhr_email']);
				$msgEmail = "Olá <b>".$_POST['vhr_nome']."</b>, <br><br>
						    A sua senha para acessar o sítio do Processo Eleitoral do CNPC foi alterada pelo administrador do sistema, seus dados para acesso são:<br><br>
							login: <b>".$_POST['vhr_login']."</b> <br>
							senha: <b>".$_POST['vhr_senha']."</b>";
				$mail->setBodyHtml($msgEmail);
				$mail->setSubject('Alteração de senha de acesso [Processo Eleitorado do CNPC]');
                $mail->send($mailTransport);
            }
			
			$db->commit();
            parent::message("$msg", "usuario/index/", "$type");
			
                
        } catch (Zend_Exception $e){
            
            $db->rollBack();
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->flashMessenger->addMessage("Desculpe, ocorreu um erro ao salvar o cadastro. Tente novamente mais tarde. ".$e->getMessage());
            $this->_helper->flashMessengerType->addMessage("ERROR");
            $this->_redirect("usuario/adicionar/");
            //parent::message("Desculpe, ocorreu um erro ao salvar o cadastro. Tente novamente mais tarde. ".$e->getMessage(), "inscricao/form-inscricao","ERROR");
	}
    }
    
    public function adicionarAction()
    {
        //perfil minico que pode acessa essa pagina
        parent::perfilMinimo(4); //perfil adminsitrador
        
        $tblSetorial = new Setorial();
        $Setoriais = $tblSetorial->buscar();
        $this->view->setoriais = $Setoriais;
        
//        $tblCadastro = new Cadastro();
//        $Eleitores = $tblCadastro->buscaEleitoresxCandidatos(array('bol_cadastroenviado = ?'=>1), true)->current();
//        $Candidatos = $tblCadastro->buscaEleitoresxCandidatos(array('bol_cadastroenviado = ?'=>1, 'int_tipocadastro = ?'=>2), true)->current();
//        
//        $this->view->eleitores = $Eleitores;
//        $this->view->candidatos = $Candidatos;
//        xd($this->view->eleitores);
//        $auth  = Zend_Auth::getInstance();
        
//        if(!$auth->hasIdentity()){
//            $this->_forward("login", "login");
//        }else{
            
//            xd('asdfad');
            
            
//        }
        //x($idUsuario);
        //xd($auth->getIdentity()->id_usuario);
    }
    
    public function salvarAction()
    {
        //perfil minico que pode acessa essa pagina
        parent::perfilMinimo(4); //perfil adminsitrador
        
        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);
        
        $email = Validacao::validarEmail($_POST['vhr_email']);
        //se for email inválido, retorna msg de erro.
        if(!$email){
            parent::message("Email inv&aacute;lido", "usuario/adicionar/", "ERROR");
        }
        
        try{

            $db->beginTransaction();
            
            $PasswordWP = new PasswordHash(8,false);
            
            //VERIFICA SE O USUARIO JA EXISTE
            $tblUsuario = new Usuario();
            $cpf = retiraMascara($_POST['vhr_login']);
            $rsUsuario = $tblUsuario->buscar(array('vhr_login = ?' => $cpf))->current();

            if(empty($rsUsuario)){
                //novo usuario
                $dadosUsuario = array( 
                                "id_perfil"  => $_POST['id_perfil'],
                                "vhr_login"  => $cpf,
                                "vhr_email"  => $_POST['vhr_email'],
                                "vhr_senha" => $PasswordWP->HashPassword($_POST['vhr_senha']),
                                "vhr_nome"  => $_POST['vhr_nome']
                                );
                $idUser = $tblUsuario->insert($dadosUsuario);
                
                if(count($_POST['id_setorial']) > 0){
                    foreach ($_POST['id_setorial'] as $idSetorial) {
                        $dadosSetoriais = array( 
                                "id_usuario"  => $idUser,
                                "id_setorial"  => $idSetorial
                                );
                        $tblUsuarioXSetorial = new UsuarioXSetorial();
                        $tblUsuarioXSetorial->inserir($dadosSetoriais);
                    }
                }
                
                $msg = 'Usu&aacute;rio cadastrado com sucesso!';
                $type = 'CONFIRM';
            } else {
                $msg = 'CPF j&aacute; cadastrado.';
                $type = 'ALERT';
            }
            
			$config = array (
				'port' => '25'
			);
			$smtp = "smtp.cultura.gov.br";			
			$mailTransport = new Zend_Mail_Transport_Smtp($smtp, $config);
			$mail = new Zend_Mail();
			$mail->setFrom('inscricoescnpc@cultura.gov.br', 'Inscrições CNPC');
			$mail->addTo($_POST['vhr_email']);
			//$msgEmail = "Segue sua senha de acesso ao sistema de Processo Eleitoral CNPC: <b>".$_POST['vhr_senha']."</b>";
			
			$msgEmail = "Olá <b>".$_POST['vhr_nome']."</b>, <br><br>
						O seu acesso ao sítio do Processo Eleitoral do CNPC foi gerado com sucesso com os seguintes dados:<br><br>
						login: <b>".$_POST['vhr_login']."</b> <br>
						senha: <b>".$_POST['vhr_senha']."</b>";
			
			$mail->setBodyHtml($msgEmail);
			$mail->setSubject('Senha de acesso ao sistema Eleitorado CNPC');
			$mail->send($mailTransport);
            
			$db->commit();			
            parent::message("$msg", "usuario/adicionar/", "$type");            
                
        } catch (Zend_Exception $e){
            
            $db->rollBack();
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->flashMessenger->addMessage("Desculpe, ocorreu um erro ao salvar o cadastro. Tente novamente mais tarde. ".$e->getMessage());
            $this->_helper->flashMessengerType->addMessage("ERROR");
            $this->_redirect("usuario/adicionar/");
            //parent::message("Desculpe, ocorreu um erro ao salvar o cadastro. Tente novamente mais tarde. ".$e->getMessage(), "inscricao/form-inscricao","ERROR");
	}
                    
    }
    
    public function alterarSenhaAction()
    {
        //VERIFICA SE O USUARIO EXISTE
        $tblUsuario = new Usuario();
        $rsUsuario = $tblUsuario->buscar(array('id_usuario = ?' => $this->_request->getParam('usu')))->current();
        $this->view->dadosUsuario = $rsUsuario;
    }
    
    public function salvarNovaSenhaAction()
    {
        $auth  = Zend_Auth::getInstance();
        //VERIFICA SE O USUARIO EXISTE
        $PasswordHash = new PasswordHash(8,false);
        $tblUsuario = new Usuario();
        $rsUsuario = $tblUsuario->buscar(array('id_usuario = ?' => $auth->getIdentity()->id_usuario))->current();
        
        if(empty($rsUsuario)){
            parent::message("Dados não conferem.", "usuario/alterar-senha/usu/".$auth->getIdentity()->id_usuario, "ERROR");
        } else {
            if($PasswordHash->CheckPassword($_POST['senhaAtual'], $rsUsuario->vhr_senha)){
                $where = array();
                $where['id_usuario = ?'] = $auth->getIdentity()->id_usuario;
                $dadosUsuario = array( 
                                "vhr_senha" => $PasswordHash->HashPassword($_POST['novaSenha']),
                                );
                $tblUsuario->alterar($dadosUsuario, $where);
                $msg = 'Senha alterada com sucesso';
                $type = 'CONFIRM';
            } else {
                $msg = 'Dados não conferem.';
                $type = 'ERROR';
            }
            parent::message("$msg", "usuario/alterar-senha/usu/".$auth->getIdentity()->id_usuario, "$type");
        }
    }
    
    public function buscarSetoriaisModalAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $cpf = retiraMascara($_POST['cpf']);
        $tblUsuario = new UsuarioXSetorial();
        $rsUsuario = $tblUsuario->buscarSetoriaisPorCpf(array('u.vhr_login = ?' => $cpf));
        
        $setoriais = '';
        
        if(count($rsUsuario)>0){
            $dados = $rsUsuario->toArray();
            $setoriais ="<ul style='padding-left:15px;'>";
            foreach ($dados as $value) {
                $setoriais .= '<li>'.utf8_encode($value['vhr_nome']).'</li>';
            }
            $setoriais .="</ul>";
        } else {
            $setoriais = '<br><br><br><center>Nenhuma setorial selecionada para este usu&aacute;rio.<center>';
        }
        echo $setoriais;
        
    }

} // fecha class