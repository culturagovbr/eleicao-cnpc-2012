<?php
/**
 * Nomes dos tipos de Inconsist�ncias Banc�rias
 * @author Equipe RUP - Politec
 * @since 11/02/2010
 * @version 1.0
 * @package application
 * @subpackage application.views.helpers
 * @copyright � 2011 - Minist�rio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_InconsistenciaBancaria
{
	/**
	 * M�todo com os tipos de inconsist�ncias banc�rias
	 * @access public
	 * @param integer $tipo
	 * @return string $dsTipo
	 */
	function inconsistenciaBancaria($tipo)
	{
		if ($tipo == 1)
		{
			$dsTipo = "O Per�odo de Execu��o n�o est� vigente.";
		}
		else if ($tipo == 2)
		{
			$dsTipo = "O Per�odo de Capta��o n�o est� vigente.";
		}
		else if ($tipo == 3)
		{
			$dsTipo = "Incentivador n�o cadastrado.";
		}
		else if ($tipo == 4)
		{
			$dsTipo = "Tipo de Dep�sito n�o foi informado.";
		}
		else if ($tipo == 5)
		{
			$dsTipo = "N�o foi poss�vel encontrar o E-mail do Proponente.";
		}
		else if ($tipo == 6)
		{
			$dsTipo = "Proponente n�o cadastrado.";
		}
		else if ($tipo == 7)
		{
			$dsTipo = "Ag�ncia e Conta Banc�ria n�o cadastrada.";
		}
		else if ($tipo == 8)
		{
			$dsTipo = "O Projeto n�o possui Enquadramento.";
		}
		else if ($tipo == 9)
		{
			$dsTipo = "N�o existe Projeto associado a Conta.";
		}
		else
		{
			$dsTipo = " ";
		}

		return $dsTipo;
	} // fecha m�todo inconsistenciaBancaria()

} // fecha class