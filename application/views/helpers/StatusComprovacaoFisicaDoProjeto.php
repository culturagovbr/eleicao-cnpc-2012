<?php
/**
 * Nomes dos status da comprovação física do projeto
 * @author Equipe RUP - Politec
 * @since 14/05/2010
 * @version 1.0
 * @package application
 * @subpackage application.views.helpers
 * @copyright © 2010 - Ministério da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_StatusComprovacaoFisicaDoProjeto
{
	/**
	 * Método com os status dos comprovantes de status do projeto
	 * @access public
	 * @param string $status
	 * @return string $nomeStatus
	 */
	function statusComprovacaoFisicaDoProjeto($status)
	{
		if ($status == 'AG')
		{
			$nomeStatus = "Aguardando Avaliação";
		}
		else if ($status == 'AV')
		{
			$nomeStatus = "Em Avaliação";
		}
		else if ($status == 'EA')
		{
			$nomeStatus = "Em Aprovação";
		}
		else if ($status == 'AD')
		{
			$nomeStatus = "Avaliado - Deferido";
		}
		else if ($status == 'AI')
		{
			$nomeStatus = "Avaliado - Indeferido";
		}
		else if ($status == 'CS')
		{
			$nomeStatus = "Comprovante Substituído";
		}
		else
		{
			$nomeStatus = "Avaliado";
		}

		return $nomeStatus;
	} // fecha método statusComprovacaoFisicaDoProjeto()

} // fecha class