<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


/* FUNÇÃO ÚTIL PARA DEBUG */

function xd($obj) {
    echo "<div style='background-color:#DFDFDF; border:1px #666666 solid; text-align:left;'>";
    echo "<pre>";
    print_r($obj);
    echo "</pre>";
    echo "</div>";
    die();
}

/* FUNÇÃO ÚTIL PARA DEBUG SEM  DIE */

function x($obj) {
    echo "<div style='background-color:#DFDFDF; border:1px #666666 solid; text-align:left;'>";
    echo "<pre>";
    print_r($obj);
    echo "</pre>";
    echo "</div>";
}

function preparaValor($valor, $tipo=1) {
    if ($tipo == 1) {
        $valor = str_replace(".", "", $valor);
        $valor = str_replace(",", ".", $valor);
    } else {
        $negativo = false;
        if (substr($valor, 0, 1) == "-") {
            $negativo = true;
            $valor = substr($valor, 1);
        }
        $valor = str_replace(",", "", $valor);
        $valor = str_replace(".", ",", $valor);
        $tamanho = strlen($valor);
        $posicao_separador = strpos($valor, ",");
        if (!$posicao_separador)
            $valor .= ",00";
        elseif (($tamanho - $posicao_separador) == 2)
            $valor .= "0";
        $valor = pontoMilhar($valor);
        if ($negativo)
            $valor = "-" . $valor;
    }
    return $valor;
}

function pontoMilhar($valor) {
    $tamanho = strlen($valor);
    $inteiro = substr($valor, 0, ($tamanho - 3));
    $decimal = substr($valor, ($tamanho - 2));
    $temp = "";
    $cont = 0;
    for ($i = (strlen($inteiro) - 1); $i >= 0; $i--) {
        $temp = substr($valor, $i, 1) . $temp;
        $cont++;
        if ($i > 0 && $cont == 3) {
            $temp = "." . $temp;
            $cont = 0;
        }
    }
    $valor = $temp . "," . $decimal;
    return $valor;
}

/**
 * Prepara o nome do arquivo enviado para upload para ser gravado no banco
 * @param  $strNomeArquivo - nome do arquivo
 * @return string
 */
function preparaNomeArquivo($strNomeArquivo) {
    //$strNomeArquivo = html_entity_decode(utf8_decode($strNomeArquivo));
    //retira acentos e substitui espaço em branco por underscores
    $arr1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç", " ", "-", "'", "/", "\\");
    $arr2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C", "", "", "", "", "");
    $strNomeArquivoAlterado = str_replace($arr1, $arr2, $strNomeArquivo);

    //transformas as letras em minúsulas
    $strNomeArquivoAlterado = strtolower($strNomeArquivoAlterado);

    return $strNomeArquivoAlterado;
}

/**
 * Apaga os caracteres da mascara do texto enviado
 * @param  $strNomeArquivo - nome do arquivo
 * @return string
 */
function retiraMascara($strNomeArquivo) {
    //retira acentos e substitui espaço em branco por underscores
    $arr1 = array("(", ")", "-", ".", "_", "/");
    $arr2 = array("", "", "", "", "", "");
    $strNomeArquivoAlterado = str_replace($arr1, $arr2, $strNomeArquivo);

    return $strNomeArquivoAlterado;
}

/**
 * Retorna o valor passado com o formato de CEP(99999-999)
 * @param  $valor - valor que deseja aplicar a mascara
 * @param  $mascara - formato da mascara que deseja aplicar
 * @return string
 */
function aplicaMascara($valor, $mascara) {
    $novoValor = null;
    $arrCaracteresEspeciais = array("-", "/", ".", ",", " ", "(", ")", "[", "]", "{", "}");

    $ct = 0;
    $tamanho = strlen($valor);
    for ($i = 0; $i < $tamanho; $i++) {
        if (!empty($mascara[$i])) {
            if (!in_array($mascara[$i], $arrCaracteresEspeciais)) {
                $novoValor .= $valor[$i - $ct];
            } else {
                $novoValor .= $mascara[$i];
                $ct++;
                $tamanho++;
            }
        }
    }
    return $novoValor;
}

function montaGuiaLinks($controller, $links = array()) {
    try {
        $pattern = "#(.*?)/$controller#is";
        if ($BASEURL = retornaBaseUrl($controller)) {

            $guia = "<div id='breadcrumb'><ul>";
            $guia .= "<li class='first'><a href='{$BASEURL}/principal/' title='In&iacute;cio'>In&iacute;cio</a></li>";
            $qtdLinks = count($links);
            $contador = 0;
            if ($qtdLinks > 0) {
                foreach ($links as $link) {
                    foreach ($link as $key => $val) {
                        $contador++;
                        if ($contador == $qtdLinks) {
                            $guia .= "<li class='last'>{$key}</li>";
                        } else {
                            $router = Zend_Controller_Front::getInstance()->getRouter();
                            if (is_array($val)) {
                                $url = $router->assemble(array('controller' => $val['controller'], 'action' => $val['action']));
                            } else {
                                $url = $val;
                            }
                            $guia .= "<li><a href='" . $url . "' title='{$key}'>" . $key . "</a></li>";
                        }
                    }
                }
            }
            $guia .= "</ul></div>";
            print $guia;
        } else {
            print "<span style='color: red;'>Erro na apresentaç?o da guia! - controller invalido</span>";
        }
    } catch (Zend_Exception $e) {
        parent::message("Erro ao montar guia de links", "projetosgerenciar/projetosgerenciar", "ERROR");
    }
}

function retornaBaseUrl($controller) {
    $BASEURL = false;
    $ESTAURL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $pattern = "#(.*?)/$controller#is";
    if (preg_match($pattern, $ESTAURL, $RESP)) {
        $BASEURL = $RESP[1];
//        $PROTOCOLO = preg_replace("#($BASEURL.*)#is", '', $_SERVER['HTTP_REFERER']);
        $PROTOCOLO = "http://";
        $BASEURL = $PROTOCOLO . $BASEURL;
    }
    return $BASEURL;
}

function montaBotaoVoltar($controller, $links) {
    $link = retornaBaseUrl($controller) . '/principal/';
    $ultimoLink = count($links);
    
    if (isset($links[$ultimoLink - 2])) {
        foreach ($links[$ultimoLink - 2] as $key => $value) {
            $titulo = $key;
            if (is_array($value) && count($value) > 0) {
                $router = Zend_Controller_Front::getInstance()->getRouter();
                $link = $router->assemble(array('controller' => $value['controller'], 'action' => $value['action']));
            } else if ($value) {
                $link = $value;
            }
        }
    }
    $botaoVoltar = "<span class='voltar'><a href='{$link}'>Voltar</a></span>";

    print($botaoVoltar);
}



/* Funcção que verifica se o Proponente está Inabilitado*/
function proponenteInabilitado($cpf)
{
	$inabilitadoDAO = new Inabilitado();
	
	$where['CgcCpf 		= ?'] = $cpf;
	$where['Habilitado 	= ?'] = 'N';
	$busca = $inabilitadoDAO->Localizar($where);
	
	if(count($busca) > 0){
		return true;
	} else {
		return false;
	}
}





// FIM DO METODO montaGuiaLinks
?>