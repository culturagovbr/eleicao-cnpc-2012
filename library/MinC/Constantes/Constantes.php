<?php

/**
* Prove constantes que encapsulam recursos utilizados pelo sistema. O 
* objetivo desta classe é diminuir o uso de strings literais durante a 
* programacao do sistema, facilitando o entendimento do codigo 
* especialmente durante manutencoes.
* @author Marcos Rodrigo Ribeiro
* @version  <pre>
*   <b>1.0</b>
*    Data: 15/10/2012
*    Autor: Marcos Rodrigo Ribeiro
*    Descricao: Versao inicial.
*   <hr>
* </pre>  
* @name Constantes()
* @access public
*/

class Constantes {

        /* =========================================================================================================== */
	/* ================== CÓDIGOS (ID's) REFERENTE À TABELA USUARIO ============================================== */
        /* =========================================================================================================== */
        
        /*=== valor do campo ID da tabela Usuario que referencia ao registro dou suario que representa o usuario para Voto Nulo e Voto Em Branco === */
	const cteIdUsuarioVotoNuloVotoEmBranco = 7542; //em homologacao=5772, em producao=7542
	
	private function __construct()
	{
		parent::__constructor();
	}
}