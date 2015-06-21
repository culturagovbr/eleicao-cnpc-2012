<?php
/**
 * Nomes dos status da comprova��o f�sica do projeto
 * @author Equipe RUP - Politec
 * @since 14/05/2010
 * @version 1.0
 * @package application
 * @subpackage application.views.helpers
 * @copyright � 2010 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_StatusComprovacaoFisicaDoProjeto
{
	/**
	 * M�todo com os status dos comprovantes de status do projeto
	 * @access public
	 * @param string $status
	 * @return string $nomeStatus
	 */
	function statusComprovacaoFisicaDoProjeto($status)
	{
		if ($status == 'AG')
		{
			$nomeStatus = "Aguardando Avalia��o";
		}
		else if ($status == 'AV')
		{
			$nomeStatus = "Em Avalia��o";
		}
		else if ($status == 'EA')
		{
			$nomeStatus = "Em Aprova��o";
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
			$nomeStatus = "Comprovante Substitu�do";
		}
		else
		{
			$nomeStatus = "Avaliado";
		}

		return $nomeStatus;
	} // fecha m�todo statusComprovacaoFisicaDoProjeto()

} // fecha class