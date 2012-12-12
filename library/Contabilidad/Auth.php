<?php
class Contabilidad_Auth {
    protected static $_instance = null;

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
            if($result->isValid()) {
                var_dump("Valid");
            } else {
                var_dump("not Valid");
            }
        }
    }
    
    public function logout(){
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
    }
    
}

