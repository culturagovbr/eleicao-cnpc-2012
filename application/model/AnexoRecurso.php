<?php 
class AnexoRecurso extends GenericModel 
{
    protected $_name = "tbAnexoRecurso";

    public function buscaCompleta($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("ar"=>$this->_name),
                    array('*')
                   );
        $slct->joinInner(array("r"=>"tbRecurso"),
                               "ar.id_recurso = r.id_recurso",
                         array()
                         );
        $slct->joinInner(array("c"=>"tbCadastro"),
                               "c.id_cadastro = r. id_cadastro",
                         array()
                         );
        /*$slct->joinLeft(array("i"=>"tbItem"),
                               "ar.id_item = i.id_item",
                         array('*')
                         );*/
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
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
