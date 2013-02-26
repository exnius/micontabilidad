<?php

class SessionController extends Zend_Controller_Action
{
    public function setpassAction()
    {
        $auth = Contabilidad_Auth::getInstance();
        $auth->logout();
        
        $this->view->isLogged = false;
    }

    public function logoutAction()
    {
        Contabilidad_Auth::getInstance()->logout();
        $this->_redirect("index");
    }
}
