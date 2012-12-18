<?php
class Contabilidad_Auth {
    protected static $_instance = null;
    private static $_user = null;
    private $_auth;

    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return (self::$_instance);
    }
    
    public function __construct() {
        $this->_auth = Zend_Auth::getInstance();
    }

    public function login($params){
        
        if(!$this->_auth->hasIdentity()){
        
            $authAdapter = Zend_Registry::get('authAdapter');
            $authAdapter->setIdentity($params['email']);
            $encryptedPass = self::encryptPassword($params['email'], $params['password']);
            $authAdapter->setCredential($encryptedPass);

            $result = $this->_auth->authenticate($authAdapter);
            $isValid = $result->isValid();
            return $isValid;
        }
        return true;
    }
    
    public function logout(){
        $this->_auth->clearIdentity();
        self::$_user = null;
    }
    
    public function getUser (){
        self::$_user = $this->_auth->hasIdentity() ? Proxy_User::getInstance()->findByEmail($this->_auth->getIdentity()) : null;
        return self::$_user;
    }
    
    public static function encryptPassword($email, $password){
        return hash_hmac('md5', $email, $password);
    }
}

