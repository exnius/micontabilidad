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
        $params = array('email' => 'andresmauriciopc@gmail.com',
                        'password' => '123456');
        Contabilidad_Auth::getInstance()->login($params);
        $this->view->pablo="gay";
    }

    public function logoutAction()
    {
        Contabilidad_Auth::getInstance()->logout();
        $this->_redirect("index");
    }
}
