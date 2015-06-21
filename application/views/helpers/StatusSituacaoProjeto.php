<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of StatusLocalRealizacaoProjeto
 *
 * @author 01129075125
 */
class Zend_View_Helper_StatusSituacaoProjeto
{

    public function StatusSituacaoProjeto($status)
    {
        switch ($status)
        {
            case 'E10': $status = "E10 - Aguarda Captação de Recursos";
            case 'E11': $status = "E11 - Encerrado Prazo de Captação";
            case 'E12': $status = "E12 - Captação Parcial";
            case 'E13': $status = "E13 - Encerrado prazo de captação - Projeto em execução";
            case 'E15': $status = "E15 - Encerrado prazo de prestação de contas";
            case 'E16': $status = "E16 - Encerrado prazo/captação - Proponente inabilitado";
        }
        return  $status;
    }


}
?>
