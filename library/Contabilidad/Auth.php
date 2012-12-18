<?php
class Contabilidad_Auth {
    protected static $_instance = null;
    private $_user = null;

    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return (self::$_instance);
    }
    
    public function login($params){
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
        
            $authAdapter = Zend_Registry::get('authAdapter');
            $authAdapter->setIdentity($params['email']);
            $authAdapter->setCredential($params['password']);

            $result = $auth->authenticate($authAdapter);
            $isValid = $result->isValid();
            $this->_user = $isValid ? Proxy_User::getInstance()->findByEmail($params['email']) : null;
            return ;
        }
        return true;
    }
    
    public function logout(){
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $this->_user = null;
    }
    
}

