<?php 
/**
 * Relatorios
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Minist?rio da Cultura - Todos os direitos reservados.
 */

class RelatoriosController extends GenericController
{
    private $intTamPag = 30;
    
    public function init()
    {
        parent::init(); // chama o init() do pai GenericControllerNew
        $auth  = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }
    } // fecha metodo init()
    /**
        * Metodo principal
        * @access public
        * @param void
        * @return void
        */
    public function indexAction()
    {
        $auth  = Zend_Auth::getInstance();
        
        if(!$auth->hasIdentity()){
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }
    }


    public function inscritosPorEstadoAction()
    {
        $idSetorial = $this->_request->getParam("setorial"); // pega o setoria via request
        $this->view->setorial = $idSetorial;

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;
        
    }

    public function listarTotaisPorEstadoAction()
    {
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout

        $arrBusca = array();
        if(!empty($idSetorial)){
            $arrBusca["id_setorial = ?"] = $idSetorial;
        }
        
        $tblCadastro = new Cadastro();
        $ordem = array('2 ASC');
        $rs = $tblCadastro->vwTotalInscritoSetorialUF($arrBusca, $ordem);
        $this->view->registros 	  = $rs;
        $this->view->idSetorial   = $idSetorial;

        $rs = $tblCadastro->vwTotalInscritoSetorialSemUF($arrBusca, $ordem);
        $this->view->registrosSemUF = $rs;
    }
    
    public function listarInscritosPorEstadoAction()
    {
        $uf = $this->_request->getParam("uf"); // pega o setoria via request
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout

        $arrBusca =array();
        if(!empty($uf)){
            $arrBusca["uf = ?"] = $uf;
        }
        if(!empty($idSetorial)){
            $arrBusca["id_setorial = ?"] = $idSetorial;
        }

        $tblUsuario = new Usuario();
        $ordem = array('6 ASC','4 ASC');
        
        if($uf == "semUF"){
            $rs = $tblUsuario->vwUsuarioSemUF(array("id_setorial = ?"=>$idSetorial), $ordem);
            $this->view->registros 	  = $rs;
        }else{
            $rs = $tblUsuario->vwUsuario($arrBusca, $ordem);
            $this->view->registros 	  = $rs;
        }
    }

    public function inscritosValidadosInvalidadosPorEstadoAction()
    {
        if(date('YmdHis') < '20120904235959'){ //real
        //if(date('YmdHis') < '20120904172500'){ //teste
            $html = '<center>
                     <table class="w900" cellpading="0" cellspacing="0" style="background-color: #fff; height: 400px;">
                     <tr>
                            <td align="center">
                                    <br>
                                    <img src="'.Zend_Controller_Front::getInstance()->getBaseUrl().'/public/img/logo-cnpc.gif"><br><br>
                            </td>
                     </tr>
                     <tr>
                            <td align="center">
                                    <h2 style="font-family:Verdana;">Informação disponível a partir <br>das 00h:00 do dia 05/09/2012</h2>
                            </td>
                     </tr>
                     </table>
                     <center><center>
                    ';
            echo $html;
            die;
        }

        $idSetorial = $this->_request->getParam("setorial"); // pega o setoria via request
        $this->view->setorial = $idSetorial;

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;
    }

    public function listarTotaisValidadosInvalidadosPorEstadoAction()
    {
        if(date('YmdHis') < '20120904235959'){ //real
        //if(date('YmdHis') < '20120904172500'){ //teste
            $html = '<center>
                     <table class="w900" cellpading="0" cellspacing="0" style="background-color: #fff; height: 400px;">
                     <tr>
                            <td align="center">
                                    <br>
                                    <img src="'.Zend_Controller_Front::getInstance()->getBaseUrl().'/public/img/logo-cnpc.gif"><br><br>
                            </td>
                     </tr>
                     <tr>
                            <td align="center">
                                    <h2 style="font-family:Verdana;">Informação disponível a partir <br>das 00h:00 do dia 05/09/2012</h2>
                            </td>
                     </tr>
                     </table>
                     <center><center>
                    ';
            echo $html;
            die;
        }
        
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout

        $arrBusca = array();
        if(!empty($idSetorial)){
            $arrBusca["id_setorial = ?"] = $idSetorial;
        }
		
        $tblCadastro = new Cadastro();
        $ordem = array('2 ASC');
        $rs = $tblCadastro->vwTotalInscritoAvaliadoSetorialUF($arrBusca, $ordem, true);
        
        $this->view->registros 	  = $rs;
        $this->view->idSetorial   = $idSetorial;

        $rs = $tblCadastro->vwTotalInscritoAvaliadoSetorialSemUF($arrBusca, $ordem, true);
        $this->view->registrosSemUF = $rs;
    }

    public function listarInscritosValidadosInvalidadosPorEstadoAction()
    {
        $uf = $this->_request->getParam("uf"); // pega o setoria via request
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout

        $arrBusca =array();
        if(!empty($uf)){
            $arrBusca["uf = ?"] = $uf;
        }
        if(!empty($idSetorial)){
            $arrBusca["id_setorial = ?"] = $idSetorial;
        }

        $tblUsuario = new Usuario();
        $ordem = array('12 ASC','4 ASC');

        if($uf == "semUF"){
            $rs = $tblUsuario->vwAvaliacaoUsuarioSemUF(array("id_setorial = ?"=>$idSetorial), $ordem);
            $this->view->registros 	  = $rs;
        }else{
            $rs = $tblUsuario->vwAvaliacaoUsuario($arrBusca, $ordem);
            $this->view->registros 	  = $rs;
        }
    }

    public function avaliacaoEleitoresAction()
    {
        $idSetorial = $this->_request->getParam("setorial"); // pega o setoria via request
        $this->view->setorial = $idSetorial;

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;
    }

    public function listarAvaliacaoEleitoresAction()
    {
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        
        $post = Zend_Registry::get('post');
        if($post->intTamPag){
            $this->intTamPag = $post->intTamPag;
        }else{
            $this->intTamPag = 30;
        }

        $pag = 1;
        if (isset($post->pag)) $pag = $post->pag;
        if (isset($post->tamPag)) $this->intTamPag = $post->tamPag;

        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim = $inicio + $this->intTamPag;

        $arrBusca = array();
        if(!empty($idSetorial)){
            $arrBusca["id_setorial = ?"] = $idSetorial;
        }
        $arrBusca["u.id_perfil = ?"] = 1;
        $arrBusca["c.int_tipocadastro = ?"] = 1;
        //$arrBusca["c.bol_validacaofinal IS NOT NULL"] = "(?)";

        $tblCadastro = new Cadastro();
        $total = 0;
        //$total = $tblCadastro->buscaCadastro($arrBusca, array(), null, null, false,true);
        $total = $tblCadastro->buscaCadastro($arrBusca, array(), null, null, true);

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) $fim = $total;

        $ordem = array();
        if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }else{$ordem = array('4 ASC');}
        $rs = $tblCadastro->buscaCadastro($arrBusca, $ordem, $tamanho, $inicio);

        $this->view->registros 	  = $rs;
        $this->view->pag 	  = $pag;
        $this->view->total 	  = $total;
        $this->view->inicio 	  = ($inicio+1);
        $this->view->fim 	  = $fim;
        $this->view->totalPag     = $totalPag;
        $this->view->intTamPag    = $this->intTamPag;
        $this->view->parametrosBusca= $_POST;
        $this->view->idSetorial   = $idSetorial;
    }
    
    public function totalInscritosValidadosInvalidadosAction()
    {
        $tbCadastro = new Cadastro();
        $rsTotalCadastroInvalidados = $tbCadastro->buscaCadastrosInabilitados(array(),null,null,null,true);
        $rsTotalCadastroValidados   = $tbCadastro->buscaCadastrosHabilitados(array(),null,null,null,true);
        $this->view->totalValidados = $rsTotalCadastroValidados;
        $this->view->totalInvalidados = $rsTotalCadastroInvalidados;
    }
    
    public function totalRecursoSolicitadosAction()
    {
        $tbRecurso = new Recurso();
        $rsTotalRecurso   = $tbRecurso->buscaCadastrosInabilitados(array(),null,null,null,true);
        $this->view->totalValidados = $rsTotalCadastroValidados;
    }
    
    public function avaliacaoInscritosAction()
    {
        $idSetorial = $this->_request->getParam("setorial"); // pega o setoria via request
        $this->view->setorial = $idSetorial;

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;
    }

    public function listarAvaliacaoInscritosAction()
    {
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        
        $post = Zend_Registry::get('post');
        if($post->intTamPag){
            $this->intTamPag = $post->intTamPag;
        }else{
            $this->intTamPag = 30;
        }

        $pag = 1;
        if (isset($post->pag)) $pag = $post->pag;
        if (isset($post->tamPag)) $this->intTamPag = $post->tamPag;

        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim = $inicio + $this->intTamPag;

        $arrBusca = array();
        if(!empty($idSetorial)){
            $arrBusca["us.id_setorial = ?"] = $idSetorial;
        }
        
        if(!empty($_POST['cpf'])){
            $arrBusca["u.vhr_login = ?"] = retiraMascara($_POST['cpf']);
        }
        if(!empty($_POST['nome'])){
            $nome = utf8_decode($_POST['nome']);
            $arrBusca["u.vhr_nome like '%{$nome}%'"] = '?';
        }
        if(!empty($_POST['setorial'])){
            $arrBusca["us.id_setorial = ?"] = $_POST['setorial'];
        }
        if(isset($_POST['idSetoriais']) && !empty($_POST['idSetoriais'])){
            $arrBusca["us.id_setorial IN (?)"] = explode(',',$_POST['idSetoriais']);
        }
        if(!empty($_POST['uf'])){
            if($_POST['uf'] == '-1'){
                $arrBusca["ci.vhr_valor IS NULL"] = "(?)";
            }else{
                $arrBusca["ci.vhr_valor = ?"] = $_POST['uf'];
            }
            
        }
        if(!empty($_POST['tipoInscricao'])){
            $arrBusca["c.int_tipocadastro = ?"] = $_POST['tipoInscricao'];
        }
        $avaliacaoPresidente = @$_POST['avaliacaoPresidente'];
        if(isset($avaliacaoPresidente) && $avaliacaoPresidente != "-1"){
            if($_POST['avaliacaoPresidente'] == '3'){
                $arrBusca["c.bol_validacaofinal IS NULL"] = "(?)";
            }else{
                $arrBusca["c.bol_validacaofinal IS NOT NULL AND c.bol_validacaofinal = ".$_POST['avaliacaoPresidente']] = "(?)";
            }
        }
        $arrBusca["u.id_perfil = ?"] = 1;
        //$arrBusca["c.int_tipocadastro = ?"] = 1;

        $tblCadastro = new Cadastro();
        $total = 0;
        //$total = $tblCadastro->buscaCadastro($arrBusca, array(), null, null, false,true);
        $total = $tblCadastro->buscaAvaliacaoCadastros($arrBusca, array(), null, null, true);

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) $fim = $total;

        $ordem = array();
        if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }else{$ordem = array('5 ASC','11 ASC');}
        $rs = $tblCadastro->buscaAvaliacaoCadastros($arrBusca, $ordem, $tamanho, $inicio);

        $this->view->registros 	  = $rs;
        $this->view->pag 	  = $pag;
        $this->view->total 	  = $total;
        $this->view->inicio 	  = ($inicio+1);
        $this->view->fim 	  = $fim;
        $this->view->totalPag     = $totalPag;
        $this->view->intTamPag    = $this->intTamPag;
        $this->view->parametrosBusca= $_POST;
        $this->view->idSetorial   = $idSetorial;
    }
    
    public function totalRecursosSolicitadosAction()
    {
        $tbCadastro = new Cadastro();
        $rsTotalCadastroInvalidados = $tbCadastro->buscaCadastrosHabilitados(array(),null,null,null,true);
        $this->view->totalInvalidados = $rsTotalCadastroInvalidados;
        
        $tbRecurso = new Recurso();
        $rsTotalRecursoSolicitado = $tbRecurso->buscar()->count();
        $this->view->totalRecursos = $rsTotalRecursoSolicitado;
    }
    
    
    
    public function recursosSolicitadosAction()
    {
        $idSetorial = $this->_request->getParam("setorial"); // pega o setoria via request
        $this->view->setorial = $idSetorial;

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;
    }

    public function listarRecursosSolicitadosAction()
    {
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        
        $post = Zend_Registry::get('post');
        if($post->intTamPag){
            $this->intTamPag = $post->intTamPag;
        }else{
            $this->intTamPag = 30;
        }

        $pag = 1;
        if (isset($post->pag)) $pag = $post->pag;
        if (isset($post->tamPag)) $this->intTamPag = $post->tamPag;

        $inicio = ($pag>1) ? ($pag-1)*$this->intTamPag : 0;
        $fim = $inicio + $this->intTamPag;

        $arrBusca = array();
        if(!empty($idSetorial)){
            $arrBusca["us.id_setorial = ?"] = $idSetorial;
        }
        
        if(!empty($_POST['cpf'])){
            $arrBusca["u.vhr_login = ?"] = retiraMascara($_POST['cpf']);
        }
        if(!empty($_POST['nome'])){
            $nome = utf8_decode($_POST['nome']);
            $arrBusca["u.vhr_nome like '%{$nome}%'"] = '?';
        }
        if(!empty($_POST['setorial'])){
            $arrBusca["us.id_setorial = ?"] = $_POST['setorial'];
        }
        if(isset($_POST['idSetoriais']) && !empty($_POST['idSetoriais'])){
            $arrBusca["us.id_setorial IN (?)"] = explode(',',$_POST['idSetoriais']);
        }
        if(!empty($_POST['uf'])){
            if($_POST['uf'] == '-1'){
                $arrBusca["ci.vhr_valor IS NULL"] = "(?)";
            }else{
                $arrBusca["ci.vhr_valor = ?"] = $_POST['uf'];
            }
            
        }
        if(!empty($_POST['tipoInscricao'])){
            $arrBusca["c.int_tipocadastro = ?"] = $_POST['tipoInscricao'];
        }
        /*$avaliacaoPresidente = @$_POST['avaliacaoPresidente'];
        if(isset($avaliacaoPresidente) && $avaliacaoPresidente != "-1"){
            if($_POST['avaliacaoPresidente'] == '3'){
                $arrBusca["c.bol_validacaofinal IS NULL"] = "(?)";
            }else{
                $arrBusca["c.bol_validacaofinal IS NOT NULL AND c.bol_validacaofinal = ".$_POST['avaliacaoPresidente']] = "(?)";
            }
        }*/
        $arrBusca["u.id_perfil = ?"] = 1;
        //$arrBusca["c.int_tipocadastro = ?"] = 1;

        $tblRecurso = new Recurso();
        $total = 0;
        //$total = $tblRecurso->buscaRecurso($arrBusca, array(), null, null, false,true);
        $total = $tblRecurso->buscarRecurso($arrBusca, array(), null, null, true);

        $totalPag = (int)(($total % $this->intTamPag == 0)?($total/$this->intTamPag):(($total/$this->intTamPag)+1));
        $tamanho = ($fim > $total) ? $total - $inicio : $this->intTamPag;
        if ($fim>$total) $fim = $total;

        $ordem = array();
        if(!empty($post->ordenacao)){ $ordem[] = "{$post->ordenacao} {$post->tipoOrdenacao}"; }else{$ordem = array('22 ASC');}
        $rs = $tblRecurso->buscarRecurso($arrBusca, $ordem, $tamanho, $inicio);

        $this->view->registros 	  = $rs;
        $this->view->pag 	  = $pag;
        $this->view->total 	  = $total;
        $this->view->inicio 	  = ($inicio+1);
        $this->view->fim 	  = $fim;
        $this->view->totalPag     = $totalPag;
        $this->view->intTamPag    = $this->intTamPag;
        $this->view->parametrosBusca= $_POST;
        $this->view->idSetorial   = $idSetorial;
    }

    public function eleitoresValidadosPorEstadoAction()
    {
        $idSetorial = $this->_request->getParam("setorial"); // pega o setoria via request
        $this->view->setorial = $idSetorial;

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;

    }

    public function listarEleitoresValidadosPorEstadoAction()
    {
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout

        $arrBusca = array();
        if(!empty($idSetorial)){
            $arrBusca["id_setorial = ?"] = $idSetorial;
        }
        $arrBusca["avaliacao_eleitor = ?"] = "validado";
        
        $tblCadastro = new Cadastro();
        $ordem = array('6 ASC', '9 asc', '5 asc');
        $rs = $tblCadastro->buscarAvaliacaoFinalInscrito($arrBusca, $ordem);
        $arr = $rs->toArray();

        $arrUF = array();
        $arrUF['AC'] = array();
        $arrUF['AL'] = array();
        $arrUF['AM'] = array();
        $arrUF['AP'] = array();
        $arrUF['BA'] = array();
        $arrUF['CE'] = array();
        $arrUF['DF'] = array();
        $arrUF['ES'] = array();
        $arrUF['GO'] = array();
        $arrUF['MA'] = array();
        $arrUF['MG'] = array();
        $arrUF['MS'] = array();
        $arrUF['MT'] = array();
        $arrUF['PA'] = array();
        $arrUF['PB'] = array();
        $arrUF['PE'] = array();
        $arrUF['PI'] = array();
        $arrUF['PR'] = array();
        $arrUF['RJ'] = array();
        $arrUF['RN'] = array();
        $arrUF['RO'] = array();
        $arrUF['RR'] = array();
        $arrUF['RS'] = array();
        $arrUF['SC'] = array();
        $arrUF['SE'] = array();
        $arrUF['SP'] = array();
        $arrUF['TO'] = array();
        $arrUF['Estado&nbsp;n&atilde;o&nbsp;informado'] = array();

        /*$arrRegistros = array();
        foreach($arr as $chave => $registro){
            $arrRegistros[$registro['uf']][$chave] = $registro['nome_inscrito'];
        }
        //xd($arrRegistros);
        $this->view->registros 	  = $arrRegistros;*/

        foreach($arr as $chave => $registro){

            if($registro['uf'] == 'AC'){
                $arrUF['AC'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'AL'){
                $arrUF['AL'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'AM'){
                $arrUF['AM'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'AP'){
                $arrUF['AP'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'BA'){
                $arrUF['BA'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'CE'){
                $arrUF['CE'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'DF'){
                $arrUF['DF'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'ES'){
                $arrUF['ES'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'GO'){
                $arrUF['GO'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'MA'){
                $arrUF['MA'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'MG'){
                $arrUF['MG'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'MS'){
                $arrUF['MS'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'MT'){
                $arrUF['MT'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'PA'){
                $arrUF['PA'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'PB'){
                $arrUF['PB'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'PE'){
                $arrUF['PE'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'PI'){
                $arrUF['PI'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'PR'){
                $arrUF['PR'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'RJ'){
                $arrUF['RJ'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'RN'){
                $arrUF['RN'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'RO'){
                $arrUF['RO'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'RR'){
                $arrUF['RR'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'RS'){
                $arrUF['RS'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'SC'){
                $arrUF['SC'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'SE'){
                $arrUF['SE'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'SP'){
                $arrUF['SP'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'TO'){
                $arrUF['TO'][] = $registro['nome_inscrito'];
            }else{
                $arrUF['Estado&nbsp;n&atilde;o&nbsp;informado'][] = $registro['nome_inscrito'];
            }
            //$arrRegistros[$registro['uf']][$chave] = $registro['nome_inscrito'];
        }
        //xd($arrUF);
        $this->view->arrUF = $arrUF;
    }

    public function candidatosValidadosPorEstadoAction()
    {
        $idSetorial = $this->_request->getParam("setorial"); // pega o setoria via request
        $this->view->setorial = $idSetorial;

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;

    }

    public function listarCandidatosValidadosPorEstadoAction()
    {
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout

        $arrBusca = array();
        if(!empty($idSetorial)){
            $arrBusca["id_setorial = ?"] = $idSetorial;
        }
        $arrBusca["avaliacao_candidato = ?"] = "validado";

        $tblCadastro = new Cadastro();
        $ordem = array('6 ASC', '9 asc', '5 asc');
        $rs = $tblCadastro->buscarAvaliacaoFinalInscrito($arrBusca, $ordem);
        $arr = $rs->toArray();

        $arrUF = array();
        $arrUF['AC'] = array();
        $arrUF['AL'] = array();
        $arrUF['AM'] = array();
        $arrUF['AP'] = array();
        $arrUF['BA'] = array();
        $arrUF['CE'] = array();
        $arrUF['DF'] = array();
        $arrUF['ES'] = array();
        $arrUF['GO'] = array();
        $arrUF['MA'] = array();
        $arrUF['MG'] = array();
        $arrUF['MS'] = array();
        $arrUF['MT'] = array();
        $arrUF['PA'] = array();
        $arrUF['PB'] = array();
        $arrUF['PE'] = array();
        $arrUF['PI'] = array();
        $arrUF['PR'] = array();
        $arrUF['RJ'] = array();
        $arrUF['RN'] = array();
        $arrUF['RO'] = array();
        $arrUF['RR'] = array();
        $arrUF['RS'] = array();
        $arrUF['SC'] = array();
        $arrUF['SE'] = array();
        $arrUF['SP'] = array();
        $arrUF['TO'] = array();
        $arrUF['Estado&nbsp;n&atilde;o&nbsp;informado'] = array();
        
        
        /*$arrRegistros = array();
        foreach($arrUF as $uf){
            foreach($arr as $chave => $registro){
                $arrRegistros[$registro['uf']][$chave] = $registro['nome_inscrito'];
            }
        }*/

        foreach($arr as $chave => $registro){

            if($registro['uf'] == 'AC'){
                $arrUF['AC'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'AL'){
                $arrUF['AL'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'AM'){
                $arrUF['AM'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'AP'){
                $arrUF['AP'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'BA'){
                $arrUF['BA'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'CE'){
                $arrUF['CE'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'DF'){
                $arrUF['DF'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'ES'){
                $arrUF['ES'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'GO'){
                $arrUF['GO'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'MA'){
                $arrUF['MA'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'MG'){
                $arrUF['MG'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'MS'){
                $arrUF['MS'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'MT'){
                $arrUF['MT'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'PA'){
                $arrUF['PA'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'PB'){
                $arrUF['PB'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'PE'){
                $arrUF['PE'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'PI'){
                $arrUF['PI'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'PR'){
                $arrUF['PR'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'RJ'){
                $arrUF['RJ'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'RN'){
                $arrUF['RN'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'RO'){
                $arrUF['RO'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'RR'){
                $arrUF['RR'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'RS'){
                $arrUF['RS'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'SC'){
                $arrUF['SC'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'SE'){
                $arrUF['SE'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'SP'){
                $arrUF['SP'][] = $registro['nome_inscrito'];
            } elseif($registro['uf'] == 'TO'){
                $arrUF['TO'][] = $registro['nome_inscrito'];
            }else{
                $arrUF['Estado&nbsp;n&atilde;o&nbsp;informado'][] = $registro['nome_inscrito'];
            }
            //$arrRegistros[$registro['uf']][$chave] = $registro['nome_inscrito'];
        }
        //xd($arrUF);
        $this->view->arrUF = $arrUF;
        //xd($arrRegistros);
        //$this->view->registros 	  = $arrRegistros;
    }

    public function totalInscritosValidadosPorEstadoAction()
    {
        $idSetorial = $this->_request->getParam("setorial"); // pega o setoria via request
        $this->view->setorial = $idSetorial;

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;

    }

    public function listarTotalInscritosValidadosPorEstadoAction()
    {
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout

        $arrBusca = array();
        if(!empty($idSetorial)){
            $arrBusca["id_setorial = ?"] = $idSetorial;
        }

        $tblCadastro = new Cadastro();
        $ordem = array('6 ASC', '9 asc', '5 asc');
        $rs = $tblCadastro->buscarAvaliacaoFinalInscrito($arrBusca, $ordem);
        $arr = $rs->toArray();

        $arrRegistros = array();
        foreach($arr as $chave => $registro){
            //if($registro['avaliacao_candidato'] == "validado")
            $arrRegistros[$registro['uf']]['eleitores'][$chave] = $registro['nome_inscrito'];
            $arrRegistros[$registro['uf']]['candidatos'][$chave] = $registro['nome_inscrito'];
        }
        //xd($arrRegistros);
        $this->view->registros 	  = $arrRegistros;
    }

    public function acompanhamentoVotacaoAction()
    {   
        $idSetorial = $this->_request->getParam("setorial"); // pega o setoria via request
        $this->view->setorial = $idSetorial;

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;

    }
    
    public function listarUfAcompanhamentoVotacaoAction()
    {
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $this->view->idSetorial = $idSetorial;
        
        $arrUF = array();
        $arrUF['AC'] = 'AC';
        $arrUF['AL'] = 'AL';
        $arrUF['AM'] = 'AM';
        $arrUF['AP'] = 'AP';
        $arrUF['BA'] = 'BA';
        $arrUF['CE'] = 'CE';
        $arrUF['DF'] = 'DF';
        $arrUF['ES'] = 'ES';
        $arrUF['GO'] = 'GO';
        $arrUF['MA'] = 'MA';
        $arrUF['MG'] = 'MG';
        $arrUF['MS'] = 'MS';
        $arrUF['MT'] = 'MT';
        $arrUF['PA'] = 'PA';
        $arrUF['PB'] = 'PB';
        $arrUF['PE'] = 'PE';
        $arrUF['PI'] = 'PI';
        $arrUF['PR'] = 'PR';
        $arrUF['RJ'] = 'RJ';
        $arrUF['RN'] = 'RN';
        $arrUF['RO'] = 'RO';
        $arrUF['RR'] = 'RR';
        $arrUF['RS'] = 'RS';
        $arrUF['SC'] = 'SC';
        $arrUF['SE'] = 'SE';
        $arrUF['SP'] = 'SP';
        $arrUF['TO'] = 'TO';
        $this->view->arrUF = $arrUF;
    }
    public function listarAcompanhamentoVotacaoAction()
    {
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request
        $uf         = $this->_request->getParam("uf");  // pega o setoria via request
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout

        $arrBusca = array();
        $arrBuscaHabilitacao = array();
        if(!empty($idSetorial)){
            $arrBusca["id_setorial = ?"] = $idSetorial;
        }
        if(!empty($idSetorial)){
            $arrBusca["chr_uf = ?"] = $uf;
        }
        $arrBuscaHabilitacao = $arrBusca;
        $arrBuscaHabilitacao['chr_habilitacao=?']=1;
        $tblHabilitacao = new Habilitarsetorialuf();
        $rsHabilitacao = $tblHabilitacao->buscar($arrBuscaHabilitacao)->current();
        $this->view->habilitado = $rsHabilitacao;

        if(!empty($rsHabilitacao))
        {
            $tblVotacao = new Votacao();
            $rs = $tblVotacao->calcularAndamentoVotacao($arrBusca);
            $this->view->dados = $rs;
        }
    }


    public function totalDeVotosRegistradosAction()
    {
        $tblAvaliacao = new AvaliacaoFinalInscrito();
        $arrBusca = array();
        $arrBusca['a.chr_avaliacao_eleitor = ?'] = 1;
        $arrBusca['h.chr_habilitacao = ?'] = 1;
        $rs = $tblAvaliacao->buscarEleitoresHabilitadosParaVotar($arrBusca)->count();
        $this->view->totalEleitores = $rs;
        
        $tblVotacao = new Votacao();
        $rs = $tblVotacao->buscarTotalDeVotos()->count();
        $this->view->totalVotos = $rs;
    }
} // fecha class