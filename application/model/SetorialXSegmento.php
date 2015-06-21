<?php 
class SetorialXSegmento extends GenericModel 
{
    protected $_name = "tbSetorialXSegmento";
    
    public function buscaCompleta($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("ss"=>$this->_name),
                    array('*')
                   );
        $slct->joinInner(array("segm"=>"tbSegmento"),
                               "ss.id_segmento = segm.id_segmento",
                         array('nomeSegmento' => 'vhr_nome')
                         );
        $slct->joinInner(array("seto"=>"tbSetorial"),
                               "ss.id_setorial = seto.id_setorial",
                         array('nomeSetorial' => 'vhr_nome')
                         );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        
        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(array("ss"=>$this->_name),
                                array("total" => "count(*)")
                                );
            $slctContador->joinInner(array("seg"=>"tbSegmento"),
                                "ss.id_segmento = seg.id_segmento",
                            array()
                            );
            $slctContador->joinInner(array("set"=>"tbSetorial"),
                                "ss.id_setorial = set.id_setorial",
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
