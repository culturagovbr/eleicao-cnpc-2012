<?php
/**
 * HabilitarsetorialufController
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class HabilitarsetorialufController extends GenericController
{
    /**
        * Metodo principal
        * @access public
        * @param void
        * @return void
        */
    public function init()
    {
        $auth  = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
            return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }
        parent::init(); // chama o init() do pai GenericControllerNew
    }
    
    public function indexAction()
    {
        $this->_forward("habilitar-setorial-uf");
    }
    
    public function habilitarSetorialUfAction()
    {
        $idSetorial = $this->_request->getParam("setorial"); // pega o setoria via request
        $this->view->setorial = $idSetorial;

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;

    }

    public function listarUfPorSetorialAction()
    {
        $idSetorial = $this->_request->getParam("idSetorial"); // pega o setoria via request

        $this->view->idSetorial = $idSetorial;
        $this->_helper->layout->disableLayout();        // Desabilita o Zend Layout

        $auth  = Zend_Auth::getInstance();
        $this->view->idUsuarioAvaliador = $auth->getIdentity()->id_usuario;

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

        $arrHabilitacao = array();
        $tbHabilitacao = new Habilitarsetorialuf();
        //VERIFICA SE ESTA SETORIAL JA FOI AVALIADA
        $rsHabilitacao = $tbHabilitacao->buscar(array('id_setorial=?'=>$idSetorial));
        if($rsHabilitacao->count() > 0){
            foreach($rsHabilitacao as $valor){
                $arrHabilitacao[$valor->chr_uf] = $valor->chr_habilitacao;
            }
        }
        //xd($arrHabilitacao);
        $this->view->arrHabilitacao = $arrHabilitacao;
    }

    
    public function salvarHabilitacaoSetorialUfAction()
    {
        $auth  = Zend_Auth::getInstance();
        $post = Zend_Registry::get("post");
        $idUsuarioAvaliador = $post->idUsuarioAvaliador;
        $idSetorial = $post->idSetorial;
        //x($_POST);

        $db = Zend_Registry :: get('db');
        $db->setFetchMode(Zend_DB :: FETCH_OBJ);

        $tbHabilitacao = new Habilitarsetorialuf();

        try{

            $db->beginTransaction();
            //VERIFICA SE ESTA SETORIAL JA FOI AVALIADA
            $rsHabilitacao = $tbHabilitacao->buscar(array('id_setorial=?'=>$idSetorial));

            //APAGA AVALIACAO SETORIAL PARA GRAVAR DE NOVO
            if($rsHabilitacao->count() > 0){
                $where = array();
                $where['id_setorial = ?'] = $idSetorial;
                $tbHabilitacao->delete($where);
            }

            foreach($_POST as $chave => $valor){

                $prefixo = substr($chave, 0, 3);
                $uf = substr($chave, 3, 2);

                if($prefixo == "rd_"){
                    //$arrDados[$uf]=$valor;
                    $arrDados['id_usuario_habilitador'] = $idUsuarioAvaliador;
                    $arrDados['id_setorial']            = $idSetorial;
                    $arrDados['chr_uf']                 = $uf;
                    $arrDados['chr_habilitacao']        = $valor;
                    $arrDados['dte_habilitacao']        = new Zend_Db_Expr('GETDATE()');
                    $tbHabilitacao->insert($arrDados);
                }
            }
            $db->commit();
            parent::message("Habilitações registradas com sucesso", "/habilitarsetorialuf", "CONFIRM");

        }catch(Exception $e){
            $db->rollBack();
            parent::message("Erro ao registrar a habilitações, tente novamente mais tarde. ".$e->getMessage(), "/habilitarsetorialuf", "ERROR");
        }
    }
    
    
} // fecha class