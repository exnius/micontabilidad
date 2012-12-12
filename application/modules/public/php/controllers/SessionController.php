<?php

class SessionController extends Zend_Controller_Action
{
    
     public function registerAction()  
    {
         $this->view->register="register";
    }
    
    public function loginAction()
    {
        $this->view->login="login";
    }
    
    public function recoverpassAction()
    {
        $this->view->recover="recover pass";
    }
    
    public function setpassAction()
    {
        $this->view->setpass="set pass";
    }
}
