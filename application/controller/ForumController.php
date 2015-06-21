<?php
/**
 * Forum
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class ForumController extends GenericController
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

        $idUsuario  = $auth->getIdentity()->id_usuario;

        //if($auth->getIdentity()->id_perfil <=1 && $auth->getIdentity()->vhr_login != "74104519200"){ //real
        //if(date('YmdHis') < '20120904180400'){ //teste
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
                                    <h2 style="font-family:Verdana;">A sala para debate virtual será liberada em breve.</h2>
                            </td>
                     </tr>
                     </table>
                     <center><center>
                    ';
            //echo $html;
            //die;
        //}


        //if($auth->getIdentity()->id_perfil <= 1 && $auth->getIdentity()->vhr_login != "74104519200"){
        if($auth->getIdentity()->id_perfil <= 1){
            $this->verificaHabilitacaoInscrito($idUsuario);
        }
        $this->view->id_perfil = $auth->getIdentity()->id_perfil;
    }
    
    public function indexAction()
    {
        //$this->_forward("habilitar-setorial-uf");
        $auth  = Zend_Auth::getInstance();

        $idUsuario  = $auth->getIdentity()->id_usuario;
            
        //SE PERFIL <> DE INSCRITO
        if($auth->getIdentity()->id_perfil != "1"){

            $idSetorial = $this->_request->getParam("setorial"); // pega o setorial via request
            $uf = $this->_request->getParam("uf"); // pega o uf via request

            if(empty($idSetorial) && empty($uf)){
                $this->_redirect("forum/escolher-forum");
                //die('parou');
            }else{
                $tbSetorial = new Setorial();
                $arrBusca = array();
                $arrBusca['id_setorial = ?'] = $idSetorial;
                $rsSetorial = $tbSetorial->buscar($arrBusca)->current();
                $this->view->setorial = $rsSetorial->vhr_nome;

                $this->view->uf = $uf;
            }
        }else{

            $tbAvaliacaoFinal = new AvaliacaoFinalInscrito();
            $arrBusca = array();
            $arrBusca['id_usuario = ?'] = $idUsuario;
            $rsUsuarioLogado = $tbAvaliacaoFinal->buscar($arrBusca)->current();
            $this->view->uf = $rsUsuarioLogado->chr_uf;
            $uf = $rsUsuarioLogado->chr_uf;

            $tbSetorial = new Setorial();
            $arrBusca = array();
            $arrBusca['id_setorial = ?'] = $rsUsuarioLogado->id_setorial;
            $rsSetorial = $tbSetorial->buscar($arrBusca)->current();
            $this->view->setorial = $rsSetorial->vhr_nome;
            $idSetorial = $rsUsuarioLogado->id_setorial; // pega o setorial via request
        }
        
        $tbAvaliacaoFinal = new AvaliacaoFinalInscrito();
        $arrBusca = array();
        $arrBusca['id_setorial = ?'] = $idSetorial;
        $arrBusca['chr_uf = ?'] = $uf;
        $arrBusca['int_tipocadastro = ?'] = 2; //SO CANDIDATOS
        $arrBusca['chr_avaliacao_candidato = ?'] = 1; //CANDIDATO VALIDADOS
        //$rsCandidatos = $tbAvaliacaoFinal->buscarCompletaCandidatos($arrBusca, array('5 asc'));
        $rsCandidatos = $tbAvaliacaoFinal->buscar($arrBusca, array('5 asc'));
        $this->view->candidatos = $rsCandidatos;

        $tbComentario = new Comentario();
        $rsComentario= array();
        $arrBusca = array();
        $arrBusca['c.id_setorial = ?'] = $idSetorial;
        $arrBusca['c.chr_uf = ?'] = $uf;
        $arrBusca['c.bol_ativo = ?'] = 1;
        //x($arrBusca);
        $rsComentario = $tbComentario->buscaCompleta($arrBusca, array('6 asc'));
        //xd($rsComentario);
        $this->view->comentarios = $rsComentario;

        $this->view->idSetorial = $idSetorial;
        $this->view->idUsuario  = $idUsuario;
    }
    
    public function detalhesCandidatoAction()
    {
        $this->_helper->layout->disableLayout();// Desabilita o Zend Layout
        $idCadastro = $this->_request->getParam("idCadastro"); // pega o setoria via request

        $tblCadastro        = new Cadastro();
        $arrBusca = array();
        $arrBusca['c.id_cadastro = ?'] = $idCadastro;
        $rsCadastro = $tblCadastro->buscaCompleta($arrBusca);

        //SETA OS VALORES DOS CAMPOS PARA ENVIAR PARA VIEW
        $this->setarValoresCamposViewForum($rsCadastro,true);
        
    }
    
    public function escolherForumAction()
    {
        $idSetorial = $this->_request->getParam("setorial"); // pega o setoria via request
        $this->view->setorial = $idSetorial;

        $tbSetorial = new Setorial();
        $rsSetorial = $tbSetorial->buscar();
        $this->view->setoriais = $rsSetorial;

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

    public function salvarComentarioAction()
    {
        //$this->_helper->layout->disableLayout();// Desabilita o Zend Layout
        
        $post = Zend_Registry::get("post");
        $idSetorial         = $post->idSetorial; 
        $idUsuario          = $post->idUsuario; 
        $uf                 = $post->uf; 
        $comentario         = $post->comentario; 

     try{

            $auth  = Zend_Auth::getInstance();

            $tblComentario      = new Comentario();
            $arrDados = array();
            $arrDados['id_setorial']    = $idSetorial;
            $arrDados['id_usuario']     = $idUsuario;
            $arrDados['chr_uf']         = $uf;
            $arrDados['vhr_comentario'] = $comentario;
            $arrDados['dte_comentario'] = new Zend_Db_Expr('GETDATE()');
            $arrDados['bol_ativo']      = 1;
            //xd($arrDados);
            $idComentario = $tblComentario->insert($arrDados);


            $auth  = Zend_Auth::getInstance();
            //SE PERFIL <> DE INSCRITO
            if($auth->getIdentity()->id_perfil != "1"){
                parent::message("Comentário registrado com sucesso", "/forum/index/?setorial={$idSetorial}&uf={$uf}&id=$idComentario", "CONFIRM");
            }else{
                parent::message("Comentário registrado com sucesso", "/forum/index/?id=$idComentario", "CONFIRM");
            }

        }catch(Exception $e){
            //xd($e);
            if($auth->getIdentity()->id_perfil != "1"){
                parent::message("Erro ao gravar o seu comentário, tente novamente mais tarde. ".$e->getMessage(), "/forum/index/?setorial={$idSetorial}&uf={$uf}", "ERROR");
            }else{
                parent::message("Erro ao gravar o seu comentário, tente novamente mais tarde. ".$e->getMessage(), "/forum", "ERROR");
            }
        }
    }

    public function listarComentariosAction()
    {
        $this->_helper->layout->disableLayout();// Desabilita o Zend Layout
        
        $idSetorial = $this->_request->getParam("setorial"); // pega o setorial via request
        $uf = $this->_request->getParam("uf"); // pega o uf via request
        
        $tbComentario = new Comentario();
        $rsComentario= array();
        $arrBusca = array();
        $arrBusca['c.id_setorial = ?'] = $idSetorial;
        $arrBusca['c.chr_uf = ?'] = $uf;
        $arrBusca['c.bol_ativo = ?'] = 1;
        //xd($arrBusca);
        $rsComentario = $tbComentario->buscaCompleta($arrBusca, array('6 asc'));
        $this->view->comentarios = $rsComentario;
    }

} // fecha class