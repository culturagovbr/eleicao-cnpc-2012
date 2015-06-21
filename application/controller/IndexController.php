<?php
/**
 * Login e autenticao
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class IndexController extends GenericController
{
    /**
        * Metodo principal
        * @access public
        * @param void
        * @return void
        */
    public function indexAction()
    {
        //$this->_forward("index", "inscricao");
        $this->_forward("login", "login");
        
        
        $auth  = Zend_Auth::getInstance();
        //xd($auth->getIdentity());
        //x($idUsuario);
        //xd($auth->getIdentity()->id_usuario);
    }
    
    
    /**
        * Efetua o logout do sistema
        * @access public
        * @param void
        * @return void
        */
    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity(); // limpa a autenticao
        unset($_SESSION);
        $this->_redirect('login');
    } // fecha logoutAction


} // fecha class