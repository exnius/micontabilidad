<?php

class SessionController extends Zend_Controller_Action
{
    public function setpassAction()
    {
        $this->view->setpass="set pass";
    }

    public function logoutAction()
    {
        Contabilidad_Auth::getInstance()->logout();
        $this->_redirect("index");
    }
}
