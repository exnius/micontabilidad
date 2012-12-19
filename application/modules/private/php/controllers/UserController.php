<?php

class Private_UserController extends Zend_Controller_Action
{
    public function findAction(){
        $this->view->pru="find";
    }
    
    public function editAction(){
        $user = Contabilidad_Auth::getInstance()->getUser();
        $this->view->user = $user;
        $currencys = Proxy_Currency::getInstance()->retrieveCurrencys();
        $this->view->currencys = $currencys;
    }
}
?>
