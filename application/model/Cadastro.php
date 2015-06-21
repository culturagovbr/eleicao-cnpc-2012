<?php  
class Cadastro extends GenericModel 
{
    protected $_name = "tbCadastro";
	//protected $_name = "Orgaos";
	
    //protected $_primary = "idAbrangencia";
    
    public function buscaCompleta($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("c"=>$this->_name),
                    array('*')
                   );
        $slct->joinInner(array("u"=>"tbUsuario"),
                               "u.id_usuario = c.id_usuario",
                         array('*')
                         );
        $slct->joinInner(array("ci"=>"tbCadastroXItem"),
                               "c.id_cadastro = ci.id_cadastro",
                         array('*')
                         );
        $slct->joinInner(array("i"=>"tbItem"),
                               "ci.id_item = i.id_item",
                         array('*')
                         );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(array("c"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("u"=>"tbUsuario"),
                                "u.id_usuario = c.id_usuario",
                                array()
                                );
            $slctContador->joinInner(array("ci"=>"tbCadastroXItem"),
                               "c.id_cadastro = ci.id_cadastro",
                              array()
                              );
            $slctContador->joinInner(array("i"=>"tbItem"),
                                "ci.id_item = i.id_item",
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
    
    public function buscaCompleta_($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg) {
        $this->buscaCompleta($where, $order, $tamanho, $inicio, $count, true);
    }
    
    public function buscaCadastro($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("c"=>$this->_name),
                    array('id_cadastro','bol_validacaofinal', 'int_tipocadastro')
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
            $slctContador->from(array("c"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("u"=>"tbUsuario"),
                                "u.id_usuario = c.id_usuario",
                                array()
                                );
            $slctContador->joinInner(array("us"=>"tbUsuarioXSetorial"),
                               "us.id_usuario = c.id_usuario",
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
    
    public function buscaEleitoresxCandidatos($where=array(), $count=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        if($count){
            $slct->from(array("c"=>$this->_name),array("total" => "count(*)"));
        } else {
            $slct->from(array("c"=>$this->_name),array('*'));
        }
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(array("c"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("u"=>"tbUsuario"),
                                "u.id_usuario = c.id_usuario",
                                array()
                                );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctContador->where($coluna, $valor);
            }

            $rs = $this->fetchAll($slctContador)->current();
            if($rs){ return $rs->total; }else{ return 0; }
        }
        
        return $this->fetchAll($slct);
    }
    
    public function buscarStatusAvaliacao($where=array(), $order=array(), $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("c"=>$this->_name),
                    array('id_cadastro',
                            'bol_validacaofinal', 
                            'int_tipocadastro',
                            'avaliacaoEleitor' => New Zend_Db_Expr("CASE WHEN bol_validacaocadastroeleitor IS NOT NULL 
                                                THEN 'true'
                                                ELSE 'false' END "),
                            'avaliacaoCandidato' => New Zend_Db_Expr("CASE WHEN bol_validacaocadastrocandidato IS NOT NULL 
                                                THEN 'true'
                                                ELSE 'false' END "),
                            'avaliacaoFinal' => New Zend_Db_Expr("CASE WHEN bol_validacaofinal IS NOT NULL 
                                                THEN 'true'
                                                ELSE 'false' END"),
                        )
                   );
        $slct->joinInner(array("u"=>"tbUsuario"),
                               "u.id_usuario = c.id_usuario",
                    array('vhr_nome','vhr_login')
                    );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        //adicionando linha order ao select
        $slct->order($order);

        if($dbg){
            xd($slct->assemble());
        }
        return $this->fetchAll($slct);
    }
    
    public function vwTotalInscritoSetorialUF($where = array(), $ordem=array()) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array('vwTotalInscritoSetorialUF'),
                array('*')
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        $slct->order($ordem);
        return $this->fetchAll($slct);
    }
    public function vwTotalInscritoSetorialSemUF($where = array(), $ordem=array()) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array('vwTotalInscritoSetorialSemUF'),
                array('*')
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        $slct->order($ordem);
        return $this->fetchAll($slct);
    }
    
    public function vwTotalInscritoAvaliadoSetorialUF($where = array(), $ordem=array(), $separar_validados_invalidados=false) {

        if($separar_validados_invalidados){
            
              $sql ="SELECT     id_setorial, uf, MAX(eleitores_validados) AS eleitores_validados,  MAX(eleitores_nao_validados) AS eleitores_nao_validados,  MAX(candidatos_validados) AS candidatos_validados,  MAX(candidatos_nao_validados) AS candidatos_nao_validados
                    FROM         (  
                                SELECT   id_setorial, uf, COUNT(uf) AS eleitores_validados,NULL AS eleitores_nao_validados,NULL AS candidatos_validados, NULL AS candidatos_nao_validados
                                FROM     dbo.vwAvaliacaoUsuario
                                WHERE    (tipocadastro = 1) AND (uf IS NOT NULL) AND (uf <> '') and bol_validacaofinal = 1
                                GROUP BY id_setorial, uf
                                UNION
                                SELECT   id_setorial, uf, NULL AS eleitores_validados, COUNT(uf) AS eleitores_nao_validados,NULL AS candidatos_validados, NULL AS candidatos_nao_validados
                                FROM     dbo.vwAvaliacaoUsuario
                                WHERE    (tipocadastro = 1) AND (uf IS NOT NULL) AND (uf <> '') and bol_validacaofinal = 0
                                GROUP BY id_setorial, uf
                                UNION
                                SELECT   id_setorial, uf,NULL AS eleitores_validados,NULL AS eleitores_nao_validados,COUNT(uf) AS candidatos_validados, NULL AS candidatos_nao_validados
                                FROM     dbo.vwAvaliacaoUsuario
                                WHERE    (tipocadastro = 2) AND (uf IS NOT NULL) AND (uf <> '') and bol_validacaofinal = 1
                                GROUP BY id_setorial, uf
                                UNION
                                SELECT   id_setorial, uf, NULL AS eleitores_validados,NULL AS eleitores_nao_validados,NULL AS candidatos_validados, COUNT(uf) AS candidatos_nao_validados
                                FROM     dbo.vwAvaliacaoUsuario
                                WHERE    (tipocadastro = 2) AND (uf IS NOT NULL) AND (uf <> '') and bol_validacaofinal = 0
                                GROUP BY id_setorial, uf
                            ) AS t";
                $ct =1;
                
                foreach ($where as $coluna => $valor) {
                    $coluna = explode("=",$coluna);
                    $coluna = $coluna[0];
                    $operador = ($ct==1) ? " WHERE " : " AND ";
                    $sql .= $operador.$coluna."= ".$valor;
                    $ct++;
                }
                $sql .=" GROUP BY id_setorial, uf";
                //xd($sql);
                $db  = Zend_Registry::get('db');
                $db->setFetchMode(Zend_DB::FETCH_OBJ);
                return $db->fetchAll($sql);
        }else{
                        
            $slct = $this->select();
            $slct->setIntegrityCheck(false);
            $slct->from(array('vwTotalInscritoAvaliadoSetorialUF'),
                    array('*'));
            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slct->where($coluna, $valor);
            }
            $slct->order($ordem);
            return $this->fetchAll($slct);
        }
    }

    public function vwTotalInscritoAvaliadoSetorialSemUF($where = array(), $ordem=array(), $separar_validados_invalidados=false) {
        
        if($separar_validados_invalidados){
            
              $sql ="SELECT     id_setorial, uf, MAX(eleitores_validados) AS eleitores_validados,  MAX(eleitores_nao_validados) AS eleitores_nao_validados,  MAX(candidatos_validados) AS candidatos_validados,  MAX(candidatos_nao_validados) AS candidatos_nao_validados
                    FROM         (  
                                SELECT   id_setorial, uf, COUNT(uf) AS eleitores_validados,NULL AS eleitores_nao_validados,NULL AS candidatos_validados, NULL AS candidatos_nao_validados
                                FROM     dbo.vwAvaliacaoUsuarioSemUF
                                WHERE    (tipocadastro = 1) AND (uf IS NULL OR uf = '') and bol_validacaofinal = 1
                                GROUP BY id_setorial, uf
                                UNION
                                SELECT   id_setorial, uf, NULL AS eleitores_validados, COUNT(uf) AS eleitores_nao_validados,NULL AS candidatos_validados, NULL AS candidatos_nao_validados
                                FROM     dbo.vwAvaliacaoUsuarioSemUF
                                WHERE    (tipocadastro = 1) AND (uf IS NULL OR uf = '') and bol_validacaofinal = 0
                                GROUP BY id_setorial, uf
                                UNION
                                SELECT   id_setorial, uf,NULL AS eleitores_validados,NULL AS eleitores_nao_validados,COUNT(uf) AS candidatos_validados, NULL AS candidatos_nao_validados
                                FROM     dbo.vwAvaliacaoUsuarioSemUF
                                WHERE    (tipocadastro = 2) AND (uf IS NULL OR uf = '') and bol_validacaofinal = 1
                                GROUP BY id_setorial, uf
                                UNION
                                SELECT   id_setorial, uf, NULL AS eleitores_validados,NULL AS eleitores_nao_validados,NULL AS candidatos_validados, COUNT(uf) AS candidatos_nao_validados
                                FROM     dbo.vwAvaliacaoUsuarioSemUF
                                WHERE    (tipocadastro = 2) AND (uf IS NULL OR uf = '') and bol_validacaofinal = 0
                                GROUP BY id_setorial, uf
                            ) AS t";
                $ct =1;
                
                foreach ($where as $coluna => $valor) {
                    $coluna = explode("=",$coluna);
                    $coluna = $coluna[0];
                    $operador = ($ct==1) ? " WHERE " : " AND ";
                    $sql .= $operador.$coluna."= ".$valor;
                    $ct++;
                }
                $sql .=" GROUP BY id_setorial, uf";
                //xd($sql);
                $db  = Zend_Registry::get('db');
                $db->setFetchMode(Zend_DB::FETCH_OBJ);
                return $db->fetchAll($sql);
        }else{
                
            $slct = $this->select();
            $slct->setIntegrityCheck(false);
            $slct->from(array('vwTotalInscritoAvaliadoSetorialSemUF'),
                    array('*')
            );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slct->where($coluna, $valor);
            }
            $slct->order($ordem);
            return $this->fetchAll($slct);
        }
    }

    public function buscaCadastrosInabilitados($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("c"=>$this->_name),
                    array('id_cadastro',
                          'bol_validacaofinal', 
                          'int_tipocadastro',
                        )
                   );
        $slct->joinInner(array("u"=>"tbUsuario"),
                               "u.id_usuario = c.id_usuario",
                        array('vhr_nome','vhr_login','vhr_email')
                    );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        $slct->where('bol_validacaofinal IS NOT NULL','?');
        $slct->where('bol_validacaofinal = ?','0');
        $slct->where('id_perfil = ?','1');
        
        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(array("c"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("u"=>"tbUsuario"),
                                "u.id_usuario = c.id_usuario",
                                array()
                                );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctContador->where($coluna, $valor);
            }
            $slctContador->where('bol_validacaofinal IS NOT NULL','?');
            $slctContador->where('bol_validacaofinal = ?','0'); //INABILITADO
            $slctContador->where('id_perfil = ?','1');
            
            $rs = $this->fetchAll($slctContador)->current();
            if($rs){ return $rs->total; }else{ return 0; }
        }
        
        //adicionando linha order ao select
        $slct->order($order);

        if($dbg){
            xd($slct->assemble());
        }
        return $this->fetchAll($slct);
    }
    
    public function buscaCadastrosHabilitados($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("c"=>$this->_name),
                    array('id_cadastro',
                          'bol_validacaofinal', 
                          'int_tipocadastro',
                        )
                   );
        $slct->joinInner(array("u"=>"tbUsuario"),
                               "u.id_usuario = c.id_usuario",
                        array('vhr_nome','vhr_login','vhr_email')
                    );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        $slct->where('bol_validacaofinal IS NOT NULL','?');
        $slct->where('bol_validacaofinal = ?','1');
        $slct->where('id_perfil = ?','1');
        
        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(array("c"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("u"=>"tbUsuario"),
                                "u.id_usuario = c.id_usuario",
                                array()
                                );

            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slctContador->where($coluna, $valor);
            }
            $slctContador->where('bol_validacaofinal IS NOT NULL','?');
            $slctContador->where('bol_validacaofinal = ?','1'); //HABILITADO
            $slctContador->where('id_perfil = ?','1');
            
            $rs = $this->fetchAll($slctContador)->current();
            if($rs){ return $rs->total; }else{ return 0; }
        }
        
        //adicionando linha order ao select
        $slct->order($order);

        if($dbg){
            xd($slct->assemble());
        }
        return $this->fetchAll($slct);
    }
    
    public function buscaAvaliacaoCadastros($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("c"=>$this->_name),
                    array('id_cadastro',
                          'bol_validacaocadastroeleitor', 
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
                          'observacaoPresidente' => 'vhr_validacaofinal',
                          )
                   );
        $slct->joinInner(array("u"=>"tbUsuario"),
                               "u.id_usuario = c.id_usuario",
                    array('nomeUsuario' =>'vhr_nome','vhr_login')
                    );
        $slct->joinInner(array("ci"=>"tbCadastroXItem"),
                               "c.id_cadastro = ci.id_cadastro AND ci.id_item=13",
                    array('UF' => 'ci.vhr_valor')
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
            $slctContador->from(array("c"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("u"=>"tbUsuario"),
                                "u.id_usuario = c.id_usuario",
                        array()
                        );
            $slctContador->joinInner(array("ci"=>"tbCadastroXItem"),
                                "c.id_cadastro = ci.id_cadastro AND ci.id_item=13",
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

    public function buscarAvaliacaoFinalInscrito($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $filtrarUF=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("c"=>$this->_name),
                    array("id_cadastro",
                          "avaliacao_eleitor" => New Zend_Db_Expr(" CASE
                                                                        WHEN EXISTS (SELECT TOP 1 * FROM tbRecurso WHERE tbRecurso.id_cadastro = C.id_cadastro) THEN
                                                                                CASE
                                                                                        WHEN chr_validacao_fase2_eleitor IS NOT NULL THEN
                                                                                                CASE
                                                                                                        WHEN chr_validacao_fase2_eleitor = 1  THEN 'validado'
                                                                                                        WHEN chr_validacao_fase2_eleitor = 2  THEN 'nao_validado'
                                                                                                END
                                                                                        WHEN chr_validacao_fase1_eleitor IS NOT NULL THEN
                                                                                                CASE
                                                                                                        WHEN chr_validacao_fase1_eleitor = 1  THEN 'validado'
                                                                                                        WHEN chr_validacao_fase1_eleitor = 2  THEN 'nao_validado'
                                                                                                END
                                                                                        ELSE 'nao_validado'
                                                                                END
                                                                        WHEN bol_validacaocadastroeleitor IS NULL THEN 'nao_validado'
                                                                        WHEN bol_validacaocadastroeleitor = 1  THEN 'validado'
                                                                        WHEN bol_validacaocadastroeleitor = 0  THEN 'nao_validado'
                                                                        ELSE 'nao_validado'
                                                                    END	"),
                        "avaliacao_candidato" => New Zend_Db_Expr(" CASE WHEN C.int_tipocadastro = 2 THEN
                                                                        CASE
                                                                            WHEN EXISTS (SELECT TOP 1 * FROM tbRecurso WHERE tbRecurso.id_cadastro = C.id_cadastro) THEN
                                                                                    CASE
                                                                                            WHEN chr_validacao_fase2_candidato IS NOT NULL THEN
                                                                                                    CASE
                                                                                                            WHEN chr_validacao_fase2_candidato = 1  THEN 'validado'
                                                                                                            WHEN chr_validacao_fase2_candidato = 2  THEN 'nao_validado'
                                                                                                    END
                                                                                            WHEN chr_validacao_fase1_candidato IS NOT NULL THEN
                                                                                                    CASE
                                                                                                            WHEN chr_validacao_fase1_candidato = 1  THEN 'validado'
                                                                                                            WHEN chr_validacao_fase1_candidato = 2  THEN 'nao_validado'
                                                                                                    END
                                                                                            ELSE 'nao_validado'
                                                                                    END
                                                                            WHEN bol_validacaocadastrocandidato IS NULL THEN 'nao_validado'
                                                                            WHEN bol_validacaocadastrocandidato = 1  THEN 'validado'
                                                                            WHEN bol_validacaocadastrocandidato = 0  THEN 'nao_validado'
                                                                            ELSE 'nao_validado'
                                                                        END
                                                                    END"))
                   );
        $slct->joinInner(array("u"=>"tbUsuario"),
                               "u.id_usuario = c.id_usuario",
                         array("id_perfil","nome_inscrito" => New Zend_Db_Expr(" CASE
                                                                                    WHEN nome.vhr_valor IS NULL OR nome.vhr_valor = '' THEN UPPER(U.vhr_nome)
                                                                                    ELSE UPPER(nome.vhr_valor)
                                                                                  END "))
                         );
        $slct->joinInner(array("us"=>"tbUsuarioXSetorial"),
                               "u.id_usuario = us.id_usuario",
                         array()
                         );
        $slct->joinInner(array("s"=>"tbSetorial"),
                               "s.id_setorial = us.id_setorial",
                         array("setorial" =>"s.vhr_nome","id_setorial")
                         );

        $slctNome = $this->select();
        $slctNome->setIntegrityCheck(false);
        $slctNome->from(array("nomeTemp"=>"tbcadastroxitem"),array('id_cadastro','vhr_valor'));
        $slctNome->where("id_item = ?",1);
        
        $slct->joinInner(array("nome"=>$slctNome),
                               "nome.id_cadastro = c.id_cadastro",
                         array('vhr_valor')
                         );

        /*if(!$filtrarUF)
        {*/
            $slct->joinInner(array("ci"=>"tbCadastroXItem"),
                                   "c.id_cadastro = ci.id_cadastro AND ci.id_item=13",
                             array("uf" => New Zend_Db_Expr(" CASE
                                                                WHEN CI.vhr_valor IS NULL OR CI.vhr_valor = '' THEN 'XX'
                                                                ELSE CI.vhr_valor
                                                              END "))
                            );
            /*$slct->joinInner(array("i"=>"tbItem"),
                                   "ci.id_item = i.id_item",
                             array()
                             );*/
        //}
        $slct->joinLeft(array("r"=>"tbRecurso"),
                               "c.id_cadastro = r.id_cadastro AND r.dte_recurso = (SELECT MAX(dte_recurso) FROM tbRecurso WHERE id_cadastro = c.id_cadastro)",
                         array()
                         );

        $slctMaster = $this->select();
        $slctMaster->setIntegrityCheck(false);
        $slctMaster->from(
                        array('selectMaster'=>$slct),
                        array('*')
                     );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slctMaster->where($coluna, $valor);
        }
        $slctMaster->where("id_perfil = ?",1); //so inscritos

        //RETORNA QTDE. DE REGISTRO PARA PAGINACAO
        if($count)
        {
            return $this->fetchAll($slctMaster)->count();
        }
        //adicionando linha order ao select
        $slctMaster->order($order);

        // paginacao
        if ($tamanho > -1) {
            $tmpInicio = 0;
            if ($inicio > -1) {
                $tmpInicio = $inicio;
            }
            $slctMaster->limit($tamanho, $tmpInicio);
        }
        //xd($slctMaster->assemble());
        return $this->fetchAll($slctMaster);
    }

    public function buscarResultadoFinalAvaliacao($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $filtrarUF=false) {

        $sql =" SELECT selectMaster.*
                    FROM   (SELECT
                                    u.id_usuario,
                                    c.id_cadastro,
                                    s.id_setorial,
                                   CASE
                                     WHEN nome.vhr_valor IS NULL
                                           OR nome.vhr_valor = '' THEN Upper(U.vhr_nome)
                                     ELSE Upper(nome.vhr_valor)
                                   END            AS nome_inscrito,

                                   CASE
                                     WHEN CI.vhr_valor IS NULL
                                           OR CI.vhr_valor = '' THEN 'XX'
                                     ELSE CI.vhr_valor
                                   END            AS uf ,
                                   CASE
                                     WHEN EXISTS (SELECT TOP 1 *
                                                  FROM   tbrecurso
                                                  WHERE  tbrecurso.id_cadastro = C.id_cadastro) THEN
                                       CASE
                                         WHEN chr_validacao_fase2_eleitor IS NOT NULL THEN
                                           CASE
                                             WHEN chr_validacao_fase2_eleitor = 1 THEN '1'
                                             WHEN chr_validacao_fase2_eleitor = 2 THEN '2'
                                           END
                                         WHEN chr_validacao_fase1_eleitor IS NOT NULL THEN
                                           CASE
                                             WHEN chr_validacao_fase1_eleitor = 1 THEN '1'
                                             WHEN chr_validacao_fase1_eleitor = 2 THEN
                                             '2'
                                           END
                                         ELSE '2'
                                       END
                                     WHEN bol_validacaocadastroeleitor IS NULL THEN '2'
                                     WHEN bol_validacaocadastroeleitor = 1 THEN '1'
                                     WHEN bol_validacaocadastroeleitor = 0 THEN '2'
                                     ELSE '2'
                                   END            AS avaliacao_eleitor,
                                   CASE
                                     WHEN C.int_tipocadastro = 2 THEN
                                       CASE
                                         WHEN EXISTS (SELECT TOP 1 *
                                                      FROM   tbrecurso
                                                      WHERE  tbrecurso.id_cadastro = C.id_cadastro)
                                       THEN
                                           CASE
                                             WHEN chr_validacao_fase2_candidato IS NOT NULL THEN
                                               CASE
                                                 WHEN chr_validacao_fase2_candidato = 1 THEN
                                                 '1'
                                                 WHEN chr_validacao_fase2_candidato = 2 THEN
                                                 '2'
                                               END
                                             WHEN chr_validacao_fase1_candidato IS NOT NULL THEN
                                               CASE
                                                 WHEN chr_validacao_fase1_candidato = 1 THEN
                                                 '1'
                                                 WHEN chr_validacao_fase1_candidato = 2 THEN
                                                 '2'
                                               END
                                             ELSE '2'
                                           END
                                         WHEN bol_validacaocadastrocandidato IS NULL THEN
                                         '2'
                                         WHEN bol_validacaocadastrocandidato = 1 THEN '1'
                                         WHEN bol_validacaocadastrocandidato = 0 THEN '2'
                                         ELSE '2'
                                       END
                                   END            AS avaliacao_candidato,
                                   c.int_tipocadastro,
                                   u.id_perfil,
                                   s.vhr_nome AS setorial
                            FROM   tbcadastro AS c
                                   INNER JOIN tbusuario AS u
                                           ON u.id_usuario = c.id_usuario
                                   INNER JOIN tbusuarioxsetorial AS us
                                           ON u.id_usuario = us.id_usuario
                                   INNER JOIN tbsetorial AS s
                                           ON s.id_setorial = us.id_setorial
                                   INNER JOIN (SELECT nomeTemp.id_cadastro,
                                                      nomeTemp.vhr_valor
                                               FROM   tbcadastroxitem AS nomeTemp
                                               WHERE  ( id_item = 1 )) AS nome
                                           ON nome.id_cadastro = c.id_cadastro
                                   INNER JOIN tbcadastroxitem AS ci
                                           ON c.id_cadastro = ci.id_cadastro
                                              AND ci.id_item = 13
                                   LEFT JOIN tbrecurso AS r
                                          ON c.id_cadastro = r.id_cadastro
                                             AND r.dte_recurso = (SELECT Max(dte_recurso)
                                                                  FROM   tbrecurso
                                                                  WHERE  id_cadastro =
                                                                 c.id_cadastro)) AS
                           selectMaster
                    WHERE ( id_perfil = 1 )
                           --( id_setorial = '9' )
                           --AND ( avaliacao_eleitor = '1' )
                           --AND
                    ORDER  BY 3 ASC,
                              5 ASC,
                              4 ASC ";
        
        $db  = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }
}
?>
