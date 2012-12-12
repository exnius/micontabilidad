<?php

class SessionController extends Zend_Controller_Action
{
    
     public function registerAction()  
    {
         $this->view->register="register";
    }
    
    public function recoverpassAction()
    {
        $this->view->recover="recover pass";
    }
    
    public function setpassAction()
    {
        $this->view->setpass="set pass";
    }
    
    public function loginAction()  
    {
        $this->view->login="login";
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity()){
        
            $authAdapter = Zend_Registry::get('authAdapter');
            $authAdapter->setIdentity('andresmauriciopc@gmail.com');
            $authAdapter->setCredential('123456');

            $result = $auth->authenticate($authAdapter);
            if($result->isValid()) {
                var_dump("Valid");
            } else {
                var_dump("not Valid");
            }
        }
        $this->view->pablo="gay";
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        $categories = Proxy_CategoryType::getInstance()->fetchAll();
        var_dump($categories);
        exit();
    }
}
