<?php
/**
 * Método para verificar o status de avaliação do cadastro
 * Trata as mensagens do sistema
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministerio da Cultura - Todos os direitos reservados.
 */

class Zend_View_Helper_StatusAvaliacaoRecurso
{
	/**
	 * Método para verificar o status de avaliação do cadastro
	 * @access public
	 * @param string idCadastro
	 * @return string mix
	 */
	public function statusAvaliacaoRecurso($idRecurso,$intTipoCadastro,$fase)
	{
            $tblRecurso = new Recurso();
            $rsRecurso = $tblRecurso->buscar(array('id_recurso = ?' => $idRecurso))->current();
            
            if($fase == '1'){
                $avaliacao_eleitor    = $rsRecurso->chr_validacao_fase1_eleitor;
                $avaliacao_candidato  = $rsRecurso->chr_validacao_fase1_candidato;
                $url = Zend_Controller_Front::getInstance()->getBaseUrl()."/recurso/form-avaliacao-recursos-fase-um/id/".$idRecurso; 
            }else{
                $avaliacao_eleitor    = $rsRecurso->chr_validacao_fase2_eleitor;
                $avaliacao_candidato  = $rsRecurso->chr_validacao_fase2_candidato;
                $url = Zend_Controller_Front::getInstance()->getBaseUrl()."/recurso/form-avaliacao-recursos-fase-dois/id/".$idRecurso; 
            }
            
			
            $retorno = "";
            $iniLink = "<a href='{$url}'>";
            $fimLink = "</a>";
            
            if($intTipoCadastro == 2)
            {
                if($avaliacao_candidato == 1 || $avaliacao_candidato == 2)
                {
                    //$retorno['status'] = "<span style='font-size:9pt; color:#66B20A;'><b>Avaliação realizada</b></span>";
                    //$retorno['icone']  = "<span style='font-size:8pt; color:#66B20A;'><b>(Recurso avaliado)</b></span><br><input type='button' name='Avaliar' id='Avaliar' class='btn_ver' onclick='window.location=\"{$url}\"'>";
					$retorno['icone']  = "<input type='button' name='Avaliar' id='Visualizar' class='btn_ver' onclick='window.location=\"{$url}\"'>";
                }else{
                    $retorno['icone']  = "<input type='button' name='Avaliar' id='Avaliar' class='btn_avaliar' onclick='window.location=\"{$url}\"'>";
                }
            }else{
                if($avaliacao_eleitor == 1 || $avaliacao_eleitor == 2)
                {
                    //$retorno['status'] = "<span style='font-size:9pt; color:#66B20A;'><b>Avaliação realizada</b></span>";
                    //$retorno['icone']  = "<span style='font-size:8pt; color:#66B20A;'><b>(Recurso avaliado)</b></span><br><input type='button' name='Avaliar' id='Avaliar' class='btn_ver' onclick='window.location=\"{$url}\"'>";
					$retorno['icone']  = "<input type='button' name='Avaliar' id='Visualizar' class='btn_ver' onclick='window.location=\"{$url}\"'>";
                }else{
                    $retorno['icone']  = "<input type='button' name='Avaliar' id='Avaliar' class='btn_avaliar' onclick='window.location=\"{$url}\"'>";
                }
            }
            
            return $retorno;
            
	} // fecha método 

} // fecha class