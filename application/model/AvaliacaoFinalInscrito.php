<?php 
class AvaliacaoFinalInscrito extends GenericModel
{
    protected $_name = "tbAvaliacaoFinalInscrito";

    public function buscarCompletaCandidatos($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("a"=>$this->_name),
                    array('*')
                   );
        $slct->joinInner(array("ci"=>"tbCadastroXItem"),
                               "a.id_cadastro = ci.id_cadastro",
                         array('*')
                         );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(array("a"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("ci"=>"tbCadastroXItem"),
                               "a.id_cadastro = ci.id_cadastro",
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
        $slct->from(array("a"=>$this->_name),
                    array('*')
                   );
        $slct->joinInner(array("s"=>"tbSetorial"),
                               "a.id_setorial = s.id_setorial",
                         array('nomeSetorial'=>'s.vhr_nome')
                    );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(array("a"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("s"=>"tbSetorial"),
                               "a.id_setorial = s.id_setorial",
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


    public function buscarEleitoresHabilitadosParaVotar($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("a"=>$this->_name),
                    array('*')
                   );
        $slct->joinInner(array("h"=>"tbHabilitacaoSetorialUf"),
                               "a.id_setorial = h.id_setorial and a.chr_uf = h.chr_uf",
                         array()
                    );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(array("a"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("h"=>"tbHabilitacaoSetorialUf"),
                               "a.id_setorial = h.id_setorial and a.chr_uf = h.chr_uf",
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
