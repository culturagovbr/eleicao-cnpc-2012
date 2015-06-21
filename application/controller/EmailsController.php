<?php
/**
 * Login e autenticao
 * @author Equipe XTI
 * @since 16.08.2012
 * @version 1.0
 * @copyright 2012 - Ministçrio da Cultura - Todos os direitos reservados.
 */

class EmailsController extends GenericController
{
    private $hostSMTP = null;
    private $port     = null;
    private $auth     = null;
    private $username = null;
    private $password = null;
    private $ssl      = null;
    private $config   = array();
    
    public function init()
    {
        $auth  = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
			return $this->_helper->redirector->goToRoute(array('controller' => 'login', 'action' => 'login'), null, true);
        }else{
            //perfil minico que pode acessa essa pagina
            parent::perfilMinimo(4); //perfil administrador
        }
        
        //$this->hostSMTP = "smtp.gmail.com";
        $this->hostSMTP = "smtp.cultura.gov.br";
        //$this->port     = "465";
        $this->port     = "25";
        //$this->auth     = "login";
        //$this->username = "xti.emails@gmail.com";
        //$this->password = "XTI_Admin";
        //$this->ssl      = "ssl";
        
        $this->config = array (
                                //'auth'     => $this->auth,
                                //'username' => $this->username,
                                //'password' => $this->password,
                                //'ssl'      => $this->ssl,
                                'port'     => $this->port
                              );
        //$config = array ('port' => '25');
        //$smtp = "smtp.cultura.gov.br";
        parent::init(); // chama o init() do pai GenericControllerNew
        
    }
    /**
        * Metodo principal
        * @access public
        * @param void
        * @return void
        */
    public function indexAction()
    {
        
    }
    
    public function formEnviarEmailInabilitadosAction(){
        
    }
    
    public function enviarEmailInabilitadosAction(){
        $post = Zend_Registry::get("post");
        
        $nome_remetente  = $post->remetente;
        $email_remetente = $post->email_remetente;
        $assunto         = $post->assunto;
        $mensagem        = $post->mensagem;
        
        $where = array();
        //$where['id_cadastro = ?'] = 5765; //cadastro renato
        $tbCadastro = new Cadastro();
        $rsCadastro = $tbCadastro->buscaCadastrosInabilitados($where);
        
        /*$arrEmails = array(
                            'marcosrr@gmail.com'=>' Marcos Ribeiro',
                            'pedrogomes0986@gmail.com'=>' Pedro Gomes',
                            'renatosm@gmail.com'=>' Renato Simões',
                            'flaviodanillo@gmail.com'=>' Flávio Danilo',
                            'henrique.flores@cultura.gov.br'=>' Henrique Flores',
                            'fabiana.lima@cultura.gov.br'=>' Fabiana Lima',
                            'francisco.ferola@cultura.gov.br'=>' Francisco Ferola',
                            'juliana.ehlert@cultura.gov.br'=>' Juliana Ehlert',
                            'maria.signorelli@cultura.gov.br'=>' Maria Signorelli',
                            'layanne.campos@cultura.gov.br'=>' Layanne Campos',
                           );*/
        
        $mailTransport = new Zend_Mail_Transport_Smtp($this->hostSMTP, $this->config);
        
        //foreach($arrEmails as $email => $nome)
        foreach($rsCadastro as $cadastro)
        {
            //xd($email.$nome);
            $nome = $cadastro->vhr_nome;
            $email = $cadastro->vhr_email;
            try{
                $mail = new Zend_Mail();
                $mail->setFrom($email_remetente, $nome_remetente); // Quem esta enviando
                $mail->addTo($email, $nome);  //para quem sera enviado
                $mail->setBodyText("Prezado(a) Sr(a). ".$nome.",\n\n".$mensagem); //texto sem formatacao
                $mail->setSubject($assunto." [".$nome."]"); //assunto
                $mail->send($mailTransport); //envia email
                unset($mail);
                sleep(3);
            }catch(Exception  $e){
                //xd($e->getMessage());
                parent::message("Falha ao enviar e-mail para :".$nome." ".$email, "emails/form-enviar-email-inabilitados/", "ERROR");
            }
        }
        parent::message("Emails enviados com sucesso!", "emails/form-enviar-email-inabilitados/", "CONFIRM");
        
    }
    public function formEnviarEmailHabilitadosAction(){
        
    }
    
    public function enviarEmailHabilitadosAction(){
        $post = Zend_Registry::get("post");
        
        $nome_remetente  = $post->remetente;
        $email_remetente = $post->email_remetente;
        $assunto         = $post->assunto;
        $mensagem        = $post->mensagem;
        
        $where = array();
        //$where['id_cadastro = ?'] = 5765; //cadastro renato
        $tbCadastro = new Cadastro();
        $rsCadastro = $tbCadastro->buscaCadastrosHabilitados($where);
        //xd($rsCadastro->toArray());

        /*$arrEmails = array(
                            'marcosrr@gmail.com'=>' Marcos Ribeiro',
                            'pedrogomes0986@gmail.com'=>' Pedro Gomes',
                            'renatosm@gmail.com'=>' Renato Simões',
                            'flaviodanillo@gmail.com'=>' Flávio Danilo',
                            'henrique.flores@cultura.gov.br'=>' Henrique Flores',
                            'fabiana.lima@cultura.gov.br'=>' Fabiana Lima',
                            'francisco.ferola@cultura.gov.br'=>' Francisco Ferola',
                            'juliana.ehlert@cultura.gov.br'=>' Juliana Ehlert',
                            'maria.signorelli@cultura.gov.br'=>' Maria Signorelli',
                            'layanne.campos@cultura.gov.br'=>' Layanne Campos',
                           );*/

        $mailTransport = new Zend_Mail_Transport_Smtp($this->hostSMTP, $this->config);

        //foreach($arrEmails as $email => $nome)
        foreach($rsCadastro as $cadastro)
        {
            $nome = $cadastro->vhr_nome;
            $email = $cadastro->vhr_email;
            try{
                $mail = new Zend_Mail();
                $mail->setFrom($email_remetente, $nome_remetente); // Quem esta enviando
                $mail->addTo($email, $nome);  //para quem sera enviado
                $mail->setBodyText("Prezado(a) Sr(a). ".$nome.",\n\n".$mensagem); //texto sem formatacao
                $mail->setSubject($assunto." [".$nome."]"); //assunto
                $mail->send($mailTransport); //envia email
                unset($mail);
                sleep(3);
            }catch(Exception  $e){
                //xd($e->getMessage());
                parent::message("Falha ao enviar e-mail para :".$nome." ".$email, "emails/form-enviar-email-habilitados/", "ERROR");
            }
        }
        
        parent::message("Email(s) enviado(s) com sucesso!", "emails/form-enviar-email-habilitados/", "CONFIRM");
        
    }

} // fecha class