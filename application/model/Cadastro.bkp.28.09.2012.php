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
}
?>
