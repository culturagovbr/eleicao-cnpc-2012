<?php 
class Comentario extends GenericModel
{
    protected $_name = "tbComentario";

    public function buscaCompleta($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("c"=>$this->_name),
                    array('*')
                   );
        $slct->joinInner(array("u"=>"tbUsuario"),
                               "c.id_usuario = u.id_usuario",
                         array('nome_usuario' => 'u.vhr_nome')
                         );
        $slct->joinLeft(array("a"=>"tbAvaliacaoFinalInscrito"),
                               "c.id_usuario = a.id_usuario",
                         array('a.int_tipocadastro',
                               'tipocadastro_final' => New Zend_Db_Expr("CASE
                                                                             WHEN int_tipocadastro = 2 THEN
                                                                               CASE
                                                                                 WHEN chr_avaliacao_candidato = 1 THEN 2 -- SE CADASTRO DE CANDIDATO ESTA VALIDADO, ENTAO SERA CANDIDATO MESMO
                                                                                 WHEN chr_avaliacao_candidato = 2 THEN 1 -- SE CADASTRO DE CANDIDATO NAO VALIDADO, ENTAO SERA ELEITOR
                                                                               END
                                                                             ELSE 1 -- SE NAO E CANDIDATO SEMPRE SERA ELEITOR
                                                                        END"),
                             )
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
            $slct->joinInner(array("u"=>"tbUsuario"),
                               "c.id_usuario = u.id_usuario",
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
