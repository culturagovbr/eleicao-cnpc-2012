<?php 
class Recurso extends GenericModel 
{
    protected $_name = "tbRecurso";
 
    public function buscarRecurso($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("r"=>$this->_name),
                    array('*',
						  'avaliacaoInstanciaUm' => New Zend_Db_Expr(" CASE 
																			WHEN c.int_tipocadastro = 2 THEN 
																				CASE 
																					WHEN chr_validacao_fase1_eleitor IS NULL AND chr_validacao_fase1_candidato IS NULL THEN '<span style=color:#16C0C9;>Aguardando análise</span>'
																					WHEN chr_validacao_fase1_eleitor = 1 AND chr_validacao_fase1_candidato = 1  THEN '<span class=green>Eleitor e Candidato Validado</span>'
																					WHEN chr_validacao_fase1_eleitor = 1 AND chr_validacao_fase1_candidato = 2  THEN '<span class=green>Eleitor Validado.</span><br /><span class=red>Candidato Não Validado</span>'
																					WHEN chr_validacao_fase1_eleitor = 2 AND chr_validacao_fase1_candidato = 1  THEN '<span class=red>Eleitor Não Validado.</span><br /><span class=green>Candidato Validado</span>'
																					WHEN chr_validacao_fase1_eleitor = 2 AND chr_validacao_fase1_candidato = 2  THEN '<span class=red>Eleitor e Candidato Não Validado</span>'
																				END						
																			WHEN c.int_tipocadastro = 1 THEN 
																				CASE 
																						WHEN chr_validacao_fase1_eleitor IS NULL THEN '<span style=color:#16C0C9;>Aguardando análise</span>'
																						WHEN chr_validacao_fase1_eleitor = 1 THEN '<span class=green>Eleitor Validado</span>'
																						WHEN chr_validacao_fase1_eleitor = 2 THEN '<span class=red>Eleitor Não Validado</span>'
																				END							
																		  END "),
						  'avaliacaoInstanciaDois' => New Zend_Db_Expr(" CASE 
																			WHEN c.int_tipocadastro = 2 THEN 
																				CASE 
																					WHEN chr_validacao_fase2_eleitor IS NULL AND chr_validacao_fase2_candidato IS NULL THEN '<span style=color:#16C0C9;>Aguardando análise</span>'
																					WHEN chr_validacao_fase2_eleitor = 1 AND chr_validacao_fase2_candidato = 1  THEN '<span class=green>Eleitor e Candidato Validado</span>'
																					WHEN chr_validacao_fase2_eleitor = 1 AND chr_validacao_fase2_candidato = 2  THEN '<span class=green>Eleitor Validado.</span><br /><span class=red>Candidato Não Validado</span>'
																					WHEN chr_validacao_fase2_eleitor = 2 AND chr_validacao_fase2_candidato = 1  THEN '<span class=red>Eleitor Não Validado.</span><br /><span class=green>Candidato Validado</span>'
																					WHEN chr_validacao_fase2_eleitor = 2 AND chr_validacao_fase2_candidato = 2  THEN '<span class=red>Eleitor e Candidato Não Validado</span>'
																				END						
																			WHEN c.int_tipocadastro = 1 THEN 
																				CASE 
																						WHEN chr_validacao_fase2_eleitor IS NULL THEN '<span style=color:#16C0C9;>Aguardando análise</span>'
																						WHEN chr_validacao_fase2_eleitor = 1 THEN '<span class=green>Eleitor Validado</span>'
																						WHEN chr_validacao_fase2_eleitor = 2 THEN '<span class=red>Eleitor Não Validado</span>'
																				END							
																		  END "),
							)
                   );
        $slct->joinInner(array("c"=>"tbCadastro"),
                               "r.id_cadastro = c.id_cadastro",
                         array( 'bol_validacaocadastroeleitor', 
                                'bol_validacaocadastrocandidato', 
                                'bol_validacaofinal', 
                                'int_tipocadastro',
                                'tipoInscricao' => New Zend_Db_Expr(" CASE 
                                                                                WHEN int_tipocadastro = 1 THEN 'Eleitor'
                                                                                WHEN int_tipocadastro = 2 THEN 'Candidato'
                                                                                ELSE 'Eleitor'
                                                                        END "),
                                'avaliacaoEleitor' => New Zend_Db_Expr(" CASE 
                                                                                WHEN bol_validacaocadastroeleitor IS NULL THEN 'Não avaliado'
                                                                                WHEN bol_validacaocadastroeleitor = 1 THEN '<span class=green>Validado</span>'
                                                                                WHEN bol_validacaocadastroeleitor = 0 THEN '<span class=red>Não validado</span>'
                                                                            END "),
                                'avaliacaoCandidato' => New Zend_Db_Expr(" CASE 
                                                                                    WHEN bol_validacaocadastrocandidato IS NULL and c.int_tipocadastro = 2 THEN  'Não avaliado'
                                                                                    WHEN bol_validacaocadastrocandidato IS NULL and c.int_tipocadastro = 1 THEN  'Não é Candidato'
                                                                                    WHEN bol_validacaocadastrocandidato = 1 THEN '<span class=green>Validado</span>'
                                                                                    WHEN bol_validacaocadastrocandidato = 0 THEN '<span class=red>Não validado</span>'
                                                                                    ELSE 'Não avaliado'
                                                                            END "),
                                'avaliacaoPresidente' => New Zend_Db_Expr(" CASE 
                                                                                    WHEN bol_validacaofinal IS NULL THEN  'Não avaliado'
                                                                                    WHEN bol_validacaofinal = 1 THEN '<span class=green>Validado</span>'
                                                                                    WHEN bol_validacaofinal = 0 THEN '<span class=red>Não validado</span>'
                                                                                    ELSE 'Não avaliado'
                                                                            END "),
                                'observacaoPresidente' => 'vhr_validacaofinal'
                            )
                    );
        $slct->joinInner(array("u"=>"tbUsuario"),
                               "u.id_usuario = c.id_usuario",
                    array('vhr_nome','vhr_login')
                    );
        $slct->joinInner(array("us"=>"tbUsuarioXSetorial"),
                               "us.id_usuario = c.id_usuario",
                    array('id_setorial')
                    );
        $slct->joinInner(array("s"=>"tbSetorial"),
                               "us.id_setorial = s.id_setorial",
                    array('nomeSetorial' => 'vhr_nome')
                    );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(array("r"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("c"=>"tbCadastro"),
                                "r.id_cadastro = c.id_cadastro",
                                array()
                                );
            $slctContador->joinInner(array("u"=>"tbUsuario"),
                                "u.id_usuario = c.id_usuario",
                                array()
                                );
            $slctContador->joinInner(array("us"=>"tbUsuarioXSetorial"),
                               "us.id_usuario = c.id_usuario",
                                array()
                                );
            $slctContador->joinInner(array("s"=>"tbSetorial"),
                               "us.id_setorial = s.id_setorial",
                                array()
                                );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctContador->where($coluna, $valor);
            }

            $rs = $this->fetchAll($slctContador)->current();
            if($rs){ return $rs->total; }else{ return 0; }
        }
        
        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        
        if($dbg){
            xd($slct->assemble());
        }
        return $this->fetchAll($slct);
    }
 
    public function buscaCompleta($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("r"=>$this->_name),
                    array('*')
                   );
        $slct->joinInner(array("ar"=>"tbAnexoRecurso"),
                               "r.id_recurso = ar.id_recurso",
                         array('*')
                    );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(array("r"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("ar"=>"tbAnexoRecurso"),
                                "r.id_recurso = ar.id_recurso",
                            array()
                        );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctContador->where($coluna, $valor);
            }

            $rs = $this->fetchAll($slctContador)->current();
            if($rs){ return $rs->total; }else{ return 0; }
        }
        
        //adicionando linha order ao select
        $slct->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slct->limit($tamanho, $tmpInicio);
        }
        
        if($dbg){
            xd($slct->assemble());
        }
        return $this->fetchAll($slct);
    }
}
?>
