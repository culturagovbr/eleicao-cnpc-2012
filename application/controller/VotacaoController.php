<?php
/**
 * Votacao
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class VotacaoController extends GenericController
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

        if(date('YmdHis') < '20121017235959'){ //real
        //if($auth->getIdentity()->id_perfil <=1 && $auth->getIdentity()->vhr_login != "74104519200"){ //teste
        //if(date('YmdHis') < '20121017153000'){ //teste
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
                                    <h2 style="font-family:Verdana;">O período de votação inicia-se <br>as 00h:00 do dia 18/10/2012.</h2>
                            </td>
                     </tr>
                     </table>
                     <center><center>
                    ';
            echo $html;
            die;
        }

        if(date('YmdHis') > '20121023235959'){  //real
        //if(date('YmdHis') > '20121017153500'){  //teste
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
                                                <h2 style="font-family:Verdana;">Período de votação encerrado.</h2>
                                        </td>
                                 </tr>
                                 </table>
                                 <center><center>
                                ';
                echo $html;
                die;
        }
        
        //if($auth->getIdentity()->id_perfil <= 1 && $auth->getIdentity()->vhr_login != "74104519200"){
        if($auth->getIdentity()->id_perfil <= 1){
            $this->verificaHabilitacaoInscrito($idUsuario);
        }
        $this->view->id_perfil = $auth->getIdentity()->id_perfil;
    }
    
    public function indexAction()
    {
        $auth  = Zend_Auth::getInstance();

        $idUsuario  = $auth->getIdentity()->id_usuario;
            
        //SE PERFIL <> DE INSCRITO
        if($auth->getIdentity()->id_perfil != "1"){

            $idSetorial = $this->_request->getParam("setorial"); // pega o setorial via request
            $uf = $this->_request->getParam("uf"); // pega o uf via request

            if(empty($idSetorial) && empty($uf)){
                $this->_redirect("votacao/escolher-sala");
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
        $arrBusca['ci.id_item = ?'] = 2; //busca o item que grava o nome fantasia do candidato
        $rsCandidatos = $tbAvaliacaoFinal->buscarCompletaCandidatos($arrBusca, array('5 asc'));
        //$rsCandidatos = $tbAvaliacaoFinal->buscar($arrBusca, array('5 asc'));
        $this->view->candidatos = $rsCandidatos;

        $this->view->idSetorial = $idSetorial;
        $this->view->idUsuarioVotante  = $idUsuario;

        //VERIFICA SE O USUARIO LOGADO JA VOTOU
        $tbVotacao = new Votacao();
        $where = array();
        $where['id_setorial = ?']       = $idSetorial;
        $where['chr_uf = ?']            = $uf;
        $where['id_usuario_votante = ?']= $idUsuario;
        $rsVotacao = $tbVotacao->buscar($where)->current();
        $this->view->bln_javotou = (!empty($rsVotacao)) ? "true" : null;
        $this->view->dadosVoto = $rsVotacao;
    }
        
    public function salvarVotoAction()
    {
        //$this->_helper->layout->disableLayout();// Desabilita o Zend Layout
        
        $post = Zend_Registry::get("post");
        $idSetorial         = $post->idSetorial; 
        $idUsuarioVotante   = $post->idUsuarioVotante;
        $uf                 = $post->uf; 
        $idCandidato        = $post->rd_candidato;

     try{

            $tblVotacao      = new Votacao();
            $arrDados = array();
            $arrDados['id_usuario_votante'] = $idUsuarioVotante;
            $arrDados['id_usuario_votado']  = $idCandidato;
            $arrDados['id_setorial']        = $idSetorial;
            $arrDados['chr_uf']             = $uf;
            $arrDados['dte_voto']           = new Zend_Db_Expr('GETDATE()');
            //xd($arrDados);
            $tblVotacao->insert($arrDados);


            $auth  = Zend_Auth::getInstance();
            //SE PERFIL <> DE INSCRITO
            if($auth->getIdentity()->id_perfil != "1"){
                parent::message("Voto registrado com sucesso", "/votacao/index/?setorial={$idSetorial}&uf={$uf}", "CONFIRM");
            }else{
                parent::message("Voto registrado com sucesso", "/votacao/index/", "CONFIRM");
            }

        }catch(Exception $e){
            //xd($e);
            $auth  = Zend_Auth::getInstance();
            if($auth->getIdentity()->id_perfil != "1"){
                parent::message("Erro ao gravar o seu voto, tente novamente mais tarde. ".$e->getMessage(), "/votacao/index/?setorial={$idSetorial}&uf={$uf}", "ERROR");
            }else{
                parent::message("Erro ao gravar o seu voto, tente novamente mais tarde. ".$e->getMessage(), "/votacao", "ERROR");
            }
        }
    }


    public function escolherSalaAction()
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

} // fecha class