<?php 
class Votacao extends GenericModel
{
    protected $_name = "tbVotacao";


    public function calcularAndamentoVotacao($where = array(), $ordem=array()) {

        $sql =" SELECT distinct
                                id_setorial,
                                chr_uf,
                                SUM(eleitores) 'eleitores',
                                SUM(candidatos) 'candidatos',
                                SUM(candidatos_votados) 'candidatos_votados',
                                SUM(eleitores_votantes) 'eleitores_votantes',
                                SUM(votos_nulos) 'votos_nulos'
                                FROM (

                        /*========== QTDE. ELEITORES ============*/
                        SELECT
                                        a.id_setorial,
                                        a.chr_uf,
                                        COUNT(id_usuario) AS 'eleitores',
                                        NULL AS 'candidatos',
                                        NULL AS 'candidatos_votados',
                                        NULL AS 'eleitores_votantes',
                                        NULL as 'votos_nulos'
                                FROM tbAvaliacaoFinalInscrito A
                        WHERE
                        chr_avaliacao_eleitor = 1
                        --AND id_setorial=2
                        --AND chr_uf='PA'
                        GROUP BY A.id_setorial,A.chr_uf

                        UNION

                        /*======== QTDE. CANDIDATOS ============*/
                        SELECT
                                        a.id_setorial,
                                        a.chr_uf,
                                        null as 'eleitores',
                                        COUNT(id_usuario) AS 'candidatos',
                                        NULL AS 'candidatos_votados',
                                        NULL AS 'eleitores_votantes',
                                        NULL as 'votos_nulos'
                                FROM tbAvaliacaoFinalInscrito A
                        WHERE
                        chr_avaliacao_candidato = 1
                        --AND id_setorial=2
                        --AND chr_uf='PA'
                        GROUP BY A.id_setorial,A.chr_uf

                        UNION

                        /*======== QTDE. CANDIDATOS VOTADOS ============*/
                        SELECT DISTINCT
                                        a.id_setorial,
                                        a.chr_uf,
                                        null as 'eleitores',
                                        null AS 'candidatos',
                                        COUNT(DISTINCT V.id_usuario_votado) AS  'candidatos_votados',
                                        NULL AS 'eleitores_votantes',
                                        NULL as 'votos_nulos'
                                FROM tbAvaliacaoFinalInscrito A
                                INNER JOIN tbVotacao V ON A.id_usuario = V.id_usuario_votado
                        WHERE
                        chr_avaliacao_candidato = 1
                        --AND a.id_setorial=2
                        --AND a.chr_uf='SC'
                        GROUP BY A.id_setorial,A.chr_uf

                        UNION

                        /*========== QTDE. ELEITORES QUE VOTARAM ============*/
                        SELECT
                                        a.id_setorial,
                                        a.chr_uf,
                                        null as 'eleitores',
                                        null AS 'candidatos',
                                        NULL AS 'candidatos_votados',
                                        COUNT(DISTINCT V.id_usuario_votante) AS  'eleitores_votantes',
                                        NULL as 'votos_nulos'
                                FROM tbAvaliacaoFinalInscrito A
                                INNER JOIN tbVotacao V ON A.id_usuario = V.id_usuario_votante
                        WHERE
                        chr_avaliacao_eleitor = 1
                        --AND a.id_setorial=2
                        --AND a.chr_uf='PA'
                        GROUP BY A.id_setorial,A.chr_uf

                        UNION

                        SELECT
                                        id_setorial,
                                        chr_uf,
                                        null as 'eleitores',
                                        null AS 'candidatos',
                                        NULL AS 'candidatos_votados',
                                        null AS  'eleitores_votantes',
                                        COUNT(id_votacao) as 'votos_nulos'
                                FROM tbVotacao
                        WHERE
                        id_usuario_votado=7542 --usuario para votos nulos
                        GROUP BY id_setorial,chr_uf

                ) selectMaster ";
        $ct =1;

        foreach ($where as $coluna => $valor) {
            $coluna = explode("=",$coluna);
            $coluna = $coluna[0];
            $operador = ($ct==1) ? " WHERE " : " AND ";
            $sql .= $operador.$coluna."= '".$valor."'";
            $ct++;
        }
        $sql .=" GROUP BY selectMaster.id_setorial, selectMaster.chr_uf";
        //xd($sql);
        $db  = Zend_Registry::get('db');
        $db->setFetchMode(Zend_DB::FETCH_OBJ);
        return $db->fetchAll($sql);
    }


    public function buscarTotalDeVotos($where=array(), $order=array(), $tamanho=-1, $inicio=-1, $count=false, $dbg=false) {

        $slct = $this->select();
        $slct->distinct();
        $slct->setIntegrityCheck(false);
        $slct->from(array("v"=>$this->_name),
                    array('id_usuario_votante')
                   );
        
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        if($count){

            $slctContador = $this->select();
            $slctContador->setIntegrityCheck(false);
            $slctContador->from(array("v"=>$this->_name),
                                array("total" => "count(*)")
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
