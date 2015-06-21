<?php 
class UsuarioXSetorial extends GenericModel 
{
    protected $_name = "tbUsuarioXSetorial";
    
    public function buscarSetoriaisPorCpf($where=array()) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("uxs" => $this->_name),
                array()
                );
        $slct->joinInner(array("u"=>"tbUsuario"),
                    "u.id_usuario = uxs.id_usuario",
                array()
                );
        $slct->joinInner(array("s"=>"tbSetorial"),
                    "uxs.id_setorial = s.id_setorial",
                array('id_setorial', 'vhr_nome')
                );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
//        xd($slct->assemble());
        return $this->fetchAll($slct);
    }
    
}
?>
