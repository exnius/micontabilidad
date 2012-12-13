<?php

class SessionController extends Zend_Controller_Action
{
    
     public function registerAction()  
    {
         $this->view->register="register";
         $params = $this->_request->getParams();
         
         var_dump($params);
//         if()
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
        $params = $this->_request->getParams();
        $session = new Contabilidad_Services_Session();
        if($this->getRequest()->isPost()){
            $resp = $session->login($params);
            if($resp['result'] == "success"){
                $this->redirect("private/index/home");
            } else {
                var_dump($resp);
            }
        }
//         $puser = Proxy_User::getInstance();
    }

    public function logoutAction()
    {
        Contabilidad_Auth::getInstance()->logout();
        $this->_redirect("index");
    }
}
