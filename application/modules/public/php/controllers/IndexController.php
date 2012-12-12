<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function homeAction()
    {
//        var_dump("heheheh");
        // action body
    }
    
    public function termsAction()  
    {
        $authAdapter = Zend_Registry::get('authAdapter');
        $authAdapter->setIdentity('andresmauriciopc@gmail.com');
        $authAdapter->setCredential('123456');
        
        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if($result->isValid()) {
            var_dump("Valid");
        } else {
            var_dump("not Valid");
        }
        $this->view->pablo="gay";
    }

    public function aboutAction()
    {
        $this->view->andres="carne de res";
    }
    
}

