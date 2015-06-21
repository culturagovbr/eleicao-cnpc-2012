<?php
/**
 * Método para verificar o status de avaliação do cadastro
 * Trata as mensagens do sistema
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class Zend_View_Helper_EstatusAvaliacaoCadastro
{
	/**
	 * Método para verificar o status de avaliação do cadastro
	 * @access public
	 * @param string idCadastro
	 * @return string mix
	 */
	public function estatusAvaliacaoCadastro($idCadastro)
	{
            $tblCadastro = new Cadastro();
            //$rsCadastro = $tblCadastro->buscarStatusAvaliacao(array('id_cadastro = ?' => $idCadastro))->current();
            $rsCadastro = $tblCadastro->buscar(array('id_cadastro = ?' => $idCadastro))->current();
            
            $bol_validacaocadastrocandidato = (isset($rsCadastro->bol_validacaocadastrocandidato)) ?  $rsCadastro->bol_validacaocadastrocandidato : null;
            $bol_validacaocadastroeleitor   = (isset($rsCadastro->bol_validacaocadastroeleitor))   ?  $rsCadastro->bol_validacaocadastroeleitor : null;
            $bol_validacaofinal             = (isset($rsCadastro->bol_validacaofinal))             ?  $rsCadastro->bol_validacaofinal : null;
            
            $url = Zend_Controller_Front::getInstance()->getBaseUrl()."avaliacao/form-avaliacao/id/".$idCadastro; 
            ////$this->Baseurl(array('controller' => 'avaliacao', 'action' => 'form-avaliacao', 'id'=>$idCadastro),null,false);
            //echo $bol_validacaocadastrocandidato." | ".$bol_validacaocadastroeleitor." | ".$bol_validacaofinal;
            
            $retorno = "";
            $iniLink = "<a href='{$url}'>";
            $fimLink = "</a>";
            
            if($bol_validacaofinal == 0){
                echo 'igual a zero';
            }elseif($bol_validacaofinal ==1){
                echo 'igual a 1';
            }elseif($bol_validacaofinal ==null){
                echo 'igual a null';
            }
            x($bol_validacaofinal);
            
            if($bol_validacaofinal == 1 || $bol_validacaofinal == 0){
                $retorno = "<span style='font-size:9pt; color:#66B20A;'><b>Avaliação finalizada</b></span>";
            }
            /*if(($bol_validacaofinal == 0 || $bol_validacaofinal == 1)){
                $retorno = "<span style='font-size:9pt; color:#66B20A;'><b>Avaliação finalizada</b></span>";
                
            }elseif($bol_validacaocadastrocandidato != null && $bol_validacaocadastroeleitor != null && $bol_validacaofinal == null){
                $retorno = "<span style='font-size:9pt; color:#16C0C9;'><b>Avaliado pelo membro da comissão</b></span>";
                
            }else{
                $retorno = "<span style='font-size:9pt; color:#FF7B00;'><b>Aguardando análise</b></span>";
            }*/
            return $iniLink.$retorno.$fimLink;
            
            // $bg = '#FF7B00';
            // $bg = '#16C0C9';
            // $bg = '#66B20A';
            
            
	} // fecha método 

} // fecha class