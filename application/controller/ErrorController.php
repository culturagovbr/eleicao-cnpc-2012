<?php
/**
 * Trata as mensagens de erro do sistema
 * @author Equipe RUP - Politec
 * @since 29/03/2010
 * @version 1.0
 * @package application
 * @subpackage application.controller
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class ErrorController extends Zend_Controller_Action
{
	/**
	 * Trata as exceções para os usuários
	 * @access public
	 * @param void
	 * @return void
	 */
	public function errorAction()
	{
		// limpa o conteúdo gerado antes do erro
		$this->getResponse()->clearBody();

		// pega a exceção e manda para o template
		$this->_helper->viewRenderer->setViewSuffix('phtml');
		$error = $this->_getParam('error_handler');
		$this->view->ambiente     = Zend_Registry::get('ambiente');
		$this->view->exception    = $error->exception;
		$this->view->request      = $error->request;
		$this->view->message_type = "ERROR";

		switch ($error->type)
		{
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				$this->getResponse()->setHttpResponseCode(404);
				$this->view->message = 'Página não encontrada!';
				break;

			default:
				$this->view->message = 'Desculpe, ocorreu algum erro no sistema, tente novamente mais tarde!';
				break;
		}
	} // fecha errorAction()
} // fecha class