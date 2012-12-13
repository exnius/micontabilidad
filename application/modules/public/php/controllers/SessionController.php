<?php

class SessionController extends Zend_Controller_Action
{
    
     public function registerAction()  
    {
         $this->view->register="register";
         $params = $this->_request->getParams();
         $puser = Proxy_User::getInstance();
         if(isset($params['full_name']) && isset($params['email']) && isset($params['password']) && isset($params['confirm_password'])){
             $user = $puser->findByEmail($params['email']);
             if($user){
                 var_dump("ya existe " . $params['email']);
             } else {
                 $user = $puser->createNew($params);
                 var_dump("creado!");
                 var_dump($user);
             }
         }
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
