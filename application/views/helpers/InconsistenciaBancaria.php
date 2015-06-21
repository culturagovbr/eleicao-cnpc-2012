<?php
/**
 * Nomes dos tipos de Inconsistкncias Bancбrias
 * @author Equipe RUP - Politec
 * @since 11/02/2010
 * @version 1.0
 * @package application
 * @subpackage application.views.helpers
 * @copyright © 2011 - Ministйrio da Cultura - Todos os direitos reservados.
 * @link http://www.cultura.gov.br
 */

class Zend_View_Helper_InconsistenciaBancaria
{
	/**
	 * Mйtodo com os tipos de inconsistкncias bancбrias
	 * @access public
	 * @param integer $tipo
	 * @return string $dsTipo
	 */
	function inconsistenciaBancaria($tipo)
	{
		if ($tipo == 1)
		{
			$dsTipo = "O Perнodo de Execuзгo nгo estб vigente.";
		}
		else if ($tipo == 2)
		{
			$dsTipo = "O Perнodo de Captaзгo nгo estб vigente.";
		}
		else if ($tipo == 3)
		{
			$dsTipo = "Incentivador nгo cadastrado.";
		}
		else if ($tipo == 4)
		{
			$dsTipo = "Tipo de Depуsito nгo foi informado.";
		}
		else if ($tipo == 5)
		{
			$dsTipo = "Nгo foi possнvel encontrar o E-mail do Proponente.";
		}
		else if ($tipo == 6)
		{
			$dsTipo = "Proponente nгo cadastrado.";
		}
		else if ($tipo == 7)
		{
			$dsTipo = "Agкncia e Conta Bancбria nгo cadastrada.";
		}
		else if ($tipo == 8)
		{
			$dsTipo = "O Projeto nгo possui Enquadramento.";
		}
		else if ($tipo == 9)
		{
			$dsTipo = "Nгo existe Projeto associado a Conta.";
		}
		else
		{
			$dsTipo = " ";
		}

		return $dsTipo;
	} // fecha mйtodo inconsistenciaBancaria()

} // fecha class