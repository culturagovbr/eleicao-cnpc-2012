<?php 
class Usuario extends GenericModel 
{
    protected $_name = "tbUsuario";
 
        /**
	 * Metodo para buscar os dados do usuario de acordo com login e senha
	 * @static
	 * @param @username (cpf ou cnpj do usuario)
	 * @param @password (senha do usuario criptografada)
	 * @return bool
	 */
	public function login($username, $password)
	{
            // busca o usuario de acordo com o login e a senha
            $slc = $this->select();
            $slc->from($this,
                            array("*")
                         );
            $slc->where('vhr_login = ?', $username);
            $slc->where('vhr_senha = ?', $password);
            $rsUsuario = $this->fetchRow($slc);

            if ($rsUsuario) // realiza a autenticao
            {
                    // configuraoes do banco
                    $dbAdapter = Zend_Db_Table::getDefaultAdapter();
                    // pegamos o zend_auth
                    $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
                    $authAdapter->setTableName($this->_name) 
                                ->setIdentityColumn('vhr_login')
                                ->setCredentialColumn('vhr_senha');

                    // seta as credenciais informada pelo usu?rio
                    $authAdapter
                                ->setIdentity($username)
                                ->setCredential($password);

                    // tenta autenticar o usuario
                    $auth   = Zend_Auth::getInstance();
                    $acesso = $auth->authenticate($authAdapter);

                    // verifica se o acesso foi permitido
                    if ($acesso->isValid())
                    {
                            // pega os dados do usu?rio com excecao da senha
                            $authData = $authAdapter->getResultRowObject(null, 'vhr_senha');
                            // armazena os dados do usuario
                            $objAuth = $auth->getStorage()->write($authData);
                            return true;
                    } // fecha if
                    else // caso nao tenha sido validado
                    {
                            return false;
                    }
            } // fecha if
            else
            {
                    return false;
            }
	} // fecha metodo login()
        
        public function buscaUsuarios($where = array(), $order = array(), $tamanho = -1, $inicio = -1, $count = false, $dbg = false) {

            $slct = $this->select();
            $slct->setIntegrityCheck(false);
            $slct->from(array("u" => $this->_name),
                    array('*')
            );
            //adiciona quantos filtros foram enviados
            foreach ($where as $coluna => $valor) {
                $slct->where($coluna, $valor);
            }

            if ($count) {

                $slctUsuarios = $this->select();
                $slctUsuarios->setIntegrityCheck(false);
                $slctUsuarios->from(array("u" => $this->_name),
                        array("total" => "count(*)")
                );

                //adiciona quantos filtros foram enviados
                foreach ($where as $coluna => $valor) {
                    $slctUsuarios->where($coluna, $valor);
                }

                $rs = $this->fetchAll($slctUsuarios)->current();
                if ($rs) {
                    return $rs->total;
                } else {
                    return 0;
                }
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

            if ($dbg) {
                xd($slct->assemble());
            }
            return $this->fetchAll($slct);
        }
    
    
    public function confirmarDadosRecuperarSenha($where = array()) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array("u" => $this->_name),
                array('id_usuario','vhr_nome','vhr_login')
        );
        $slct->joinInner(array("c"=>"tbCadastro"),
                            "u.id_usuario = c.id_usuario",
                array()
                );
        $slct->joinInner(array("ci"=>"tbCadastroXItem"),
                            "c.id_cadastro = ci.id_cadastro",
                array()
                );
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }

        return $this->fetchRow($slct);
    }


    public function vwUsuario($where = array(), $ordem=array()) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array('vwUsuario'),
                array('*')
        );
        
        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        $slct->order($ordem);
        return $this->fetchAll($slct);
    }

    public function vwAvaliacaoUsuario($where = array(), $ordem=array()) {

        $slct = $this->select();
        $slct->setIntegrityCheck(false);
        $slct->from(array('vwAvaliacaoUsuario'),
                array('*')
        );

        //adiciona quantos filtros foram enviados
        foreach ($where as $coluna => $valor) {
            $slct->where($coluna, $valor);
        }
        $slct->order($ordem);
        return $this->fetchAll($slct);
    }
}
?>
