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
}
?>
