<?php

class SessionController extends Zend_Controller_Action
{
    public function setpassAction()
    {
    }

    public function logoutAction()
    {
        Contabilidad_Auth::getInstance()->logout();
        $this->_redirect("index");
    }
}
