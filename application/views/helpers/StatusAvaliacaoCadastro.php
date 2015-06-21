<?php
/**
 * Método para verificar o status de avaliação do cadastro
 * Trata as mensagens do sistema
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministerio da Cultura - Todos os direitos reservados.
 */

class Zend_View_Helper_StatusAvaliacaoCadastro
{
	/**
	 * Método para verificar o status de avaliação do cadastro
	 * @access public
	 * @param string idCadastro
	 * @return string mix
	 */
	public function statusAvaliacaoCadastro($idCadastro)
	{
            $tblCadastro = new Cadastro();
            $rsCadastro = $tblCadastro->buscarStatusAvaliacao(array('id_cadastro = ?' => $idCadastro))->current();
			
			$bol_validacaocadastroeleitor	 = (isset($rsCadastro->avaliacaoEleitor)) 	?  $rsCadastro->avaliacaoEleitor : 'false';
            $bol_validacaocadastrocandidato  = (isset($rsCadastro->avaliacaoCandidato))  ?  $rsCadastro->avaliacaoCandidato : 'false';
            $bol_validacaofinal              = (isset($rsCadastro->avaliacaoFinal))      ?  $rsCadastro->avaliacaoFinal : 'false';
			$int_tipocadastro                = (isset($rsCadastro->int_tipocadastro))    ?  $rsCadastro->int_tipocadastro : null;
			
			$url = Zend_Controller_Front::getInstance()->getBaseUrl()."/avaliacao/form-avaliacao/id/".$idCadastro; 
			//xd($rsCadastro);
			//echo($bol_validacaocadastrocandidato."-".$bol_validacaocadastroeleitor."-".$bol_validacaofinal."-".$int_tipocadastro);
			
            $retorno = "";
            $iniLink = "<a href='{$url}'>";
            $fimLink = "</a>";
			$botam	 = "<a href='{$url}'>";
            
			
            if($bol_validacaofinal == "true"){
                $retorno['status'] = "<span style='font-size:9pt; color:#66B20A;'><b>Avaliação finalizada</b></span>";
				$retorno['icone']  = "<input type='button' name='visualizar' id='visualizar' class='btn_ver' onclick='window.location=\"{$url}\"'>";
            }elseif(($bol_validacaocadastroeleitor == "true" && $bol_validacaocadastrocandidato == "true" && $bol_validacaofinal == "false")){
				$retorno['status'] = "<span style='font-size:9pt; color:#FF7B00;'><b>Aguardando habilitação</b></span>";
				$retorno['icone']  = "<input type='button' name='avaliar' id='avaliar' class='btn_avaliar' onclick='window.location=\"{$url}\"'>";
                
            }elseif($bol_validacaocadastroeleitor == "true" && $bol_validacaocadastrocandidato == "false" && $int_tipocadastro == "2"){
                $retorno['status'] = "<span style='font-size:9pt; color:#FF7B00;'><b>Em análise</b></span>";
				$retorno['icone']  = "<input type='button' name='avaliar' id='avaliar' class='btn_avaliar' onclick='window.location=\"{$url}\"'>";
                
            }elseif($bol_validacaocadastroeleitor == "true" && $bol_validacaocadastrocandidato == "false" && $int_tipocadastro == "1"){
                $retorno['status'] = "<span style='font-size:9pt; color:#FF7B00;'><b>Aguardando habilitação</b></span>";
				$retorno['icone']  = "<input type='button' name='avaliar' id='avaliar' class='btn_avaliar' onclick='window.location=\"{$url}\"'>";
                
            }elseif($bol_validacaocadastroeleitor == "false" && $bol_validacaocadastrocandidato == "false"){
                $retorno['status'] = "<span style='font-size:9pt; color:#16C0C9;'><b>Aguardando análise</b></span>";
				$retorno['icone']  = "<input type='button' name='avaliar' id='avaliar' class='btn_avaliar' onclick='window.location=\"{$url}\"'>";
                
            }else{
                $retorno['status'] ="<span style='font-size:9pt; color:#FF7B00;'><b>Aguardando análise</b></span>";
				$retorno['icone']  = "<input type='button' name='avaliar' id='avaliar' class='btn_avaliar' onclick='window.location=\"{$url}\"'>";
            }
			return $retorno;
			
            //return $iniLink.$retorno.$fimLink;
            
            // $bg = '#FF7B00';
            // $bg = '#16C0C9';
            // $bg = '#66B20A';
            
            
	} // fecha método 

} // fecha class