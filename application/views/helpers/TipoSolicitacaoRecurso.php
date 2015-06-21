<?php
/**
 * Tipos de solicitaчѕes dos recursos
 * @author emanuel.sampaio - Politec
 * @since 12/03/2011
 * @version 1.0
 * @package application
 * @subpackage application.view.helpers
 * @copyright Љ 2011 - Ministщrio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_TipoSolicitacaoRecurso
{
	/**
	 * Mщtodo com os tipos de solicitaчѕes dos recursos
	 * @access public
	 * @param string $tp
	 * @return string
	 */
	public function tipoSolicitacaoRecurso($tp)
	{
		$tp = trim($tp);

		if ($tp == 'PI')
		{
			$ds = "Projeto Indeferido";
		}
		else if ($tp == 'EN')
		{
			$ds = "Projeto Aprovado - Enquadramento";
		}
		else if ($tp == 'OR')
		{
			$ds = "Projeto Aprovado - Orчamento";
		}
		else if ($tp == 'PP')
		{
			$ds = "Prorrogaчуo de Prazo de Captaчуo";
		}
		else if ($tp == 'PE')
		{
			$ds = "Prorrogaчуo de Prazo de Execuчуo";
		}
		else if ($tp == 'PC')
		{
			$ds = "Prestaчуo de Contas";
		}

		return $ds;
	} // fecha mщtodo tipoSolicitacaoRecurso()

} // fecha class