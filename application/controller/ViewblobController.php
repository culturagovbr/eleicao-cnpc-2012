<?php
/**
 * Login e autenticao
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class ViewblobController extends GenericController
{
    public function init()
    {
        $auth  = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
			return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }
        parent::init(); // chama o init() do pai GenericControllerNew
        
    }
    /**
        * Metodo principal
        * @access public
        * @param void
        * @return void
        */
    public function indexAction()
    {
        $auth  = Zend_Auth::getInstance();
        //xd($auth->getIdentity());
        
        $hash = $this->_request->getParam('id'); //hash de acesso ao arquivo
        $hash = base64_decode($hash);
        //xd($hash);
        $hash =(explode('/',$hash));
        //xd($hash);
        $cpf = $hash[count($hash)-2];
        $nomeArquivo = $hash[count($hash)-1];
        if($auth->hasIdentity())
        {
            $idUsuario = $auth->getIdentity()->id_usuario;
            
            //proprio usuario ou perfis superiores visualizam o arquivo
            if($auth->getIdentity()->id_perfil > 1 || $auth->getIdentity()->vhr_login == $cpf){
                
                $arquivo = getcwd() ."/public/anexos/".$cpf."/".$nomeArquivo;
                //xd($arquivo);
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-type: application/pdf');
                header('Content-disposition: attachment; filename="'.basename($nomeArquivo).'";');
                header("Content-Transfer-Encoding: binary");
                header('Content-Length: '.filesize($arquivo));
                readfile($arquivo);
                exit;
            }else{
                $this->_helper->viewRenderer->setNoRender(true);
                $this->_helper->flashMessenger->addMessage("Você não permissão para visualizar esta informação");
                $this->_helper->flashMessengerType->addMessage("ERROR");
                $this->_redirect("inscricao/form-inscricao/usu/{$idUsuario}");   
            }
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }else{
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }
    }
    
    public function recursoAction()
    {
        $auth  = Zend_Auth::getInstance();
        //xd($auth->getIdentity());
        
        $hash = $this->_request->getParam('id'); //hash de acesso ao arquivo
        $hash = base64_decode($hash);
        //x($hash);
        $hash =(explode('/',$hash));
        //x($hash);
        $cpf = $hash[count($hash)-3];
        $nomeArquivo = $hash[count($hash)-1];
        //x($cpf);
        //xd($nomeArquivo);
        if($auth->hasIdentity())
        {
            $idUsuario = $auth->getIdentity()->id_usuario;
            
            //proprio usuario ou perfis superiores visualizam o arquivo
            if($auth->getIdentity()->id_perfil > 1 || $auth->getIdentity()->vhr_login == $cpf){
                
                $arquivo = getcwd() ."/public/anexos/".$cpf."/recurso/".$nomeArquivo;
                //xd($arquivo);
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-type: application/pdf');
                header('Content-disposition: attachment; filename="'.basename($nomeArquivo).'";');
                header("Content-Transfer-Encoding: binary");
                header('Content-Length: '.filesize($arquivo));
                readfile($arquivo);
                exit;
            }else{
                $this->_helper->viewRenderer->setNoRender(true);
                $this->_helper->flashMessenger->addMessage("Você não permissão para visualizar esta informação");
                $this->_helper->flashMessengerType->addMessage("ERROR");
                $this->_redirect("recurso/form-recurso/");   
            }
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }else{
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }
    }

    public function forumAction()
    {
        $auth  = Zend_Auth::getInstance();
        //xd($auth->getIdentity());

        $hash = $this->_request->getParam('id'); //hash de acesso ao arquivo
        $hash = base64_decode($hash);
        //xd($hash);
        $hash =(explode('/',$hash));
        //xd($hash);
        
        $nomeArquivo = $hash[count($hash)-1];
        if($hash[count($hash)-2] == "recurso")
        {
            $cpf = $hash[count($hash)-3];
            $arquivo = getcwd() ."/public/anexos/".$cpf."/recurso/".$nomeArquivo;
        }else{
            $cpf = $hash[count($hash)-2];
            $arquivo = getcwd() ."/public/anexos/".$cpf."/".$nomeArquivo;
        }

        //comprovante_atuacao_setor.pdf
        //comprovante_funcao_comissionado.pdf
        $arrArquivosPermitidos = array("curriculo.pdf","portfolio.pdf","carta_apoio.pdf");

        if($auth->hasIdentity())
        {
            $idUsuario = $auth->getIdentity()->id_usuario;

            //somente arquivos que podem ser visualizados pelo forum. arquivos dos candidatos, que sao publicos e nao pessoais
            //OBS: FOI ABERTO PARA VISUALIZAR QUALQUER ARUQUIVO PQ NA FASE DE RECURSO O ARQUIVO PODE CONTER QUALQUER NOME
            //if(in_array($nomeArquivo, $arrArquivosPermitidos)){

                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-type: application/pdf');
                header('Content-disposition: attachment; filename="'.basename($nomeArquivo).'";');
                header("Content-Transfer-Encoding: binary");
                header('Content-Length: '.filesize($arquivo));
                readfile($arquivo);
                exit;
            /*}else{
                $this->_helper->viewRenderer->setNoRender(true);
                $this->_helper->flashMessenger->addMessage("Você não permissão para visualizar esta informação");
                $this->_helper->flashMessengerType->addMessage("ERROR");
                $this->_redirect("login/error");
            }*/
            //return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }else{
            $this->_helper->viewRenderer->setNoRender(true);
            $this->_helper->flashMessenger->addMessage("Você não permissão para visualizar esta informação");
            $this->_helper->flashMessengerType->addMessage("ERROR");
            $this->_redirect("login/error");
            //return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }
    }

} // fecha class