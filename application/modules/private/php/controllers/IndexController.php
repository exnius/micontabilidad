<?php

class Private_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function homeAction()
    {
      $user = Contabilidad_Auth::getInstance()->getUser();
      $this->view->accounts = Proxy_Account::getInstance()->retrieveByUserId($user->id);
      $this->view->currencys = Proxy_Currency::getInstance()->retrieveCurrencys();
      $this->view->user = $user;
      
//      $accounts = Proxy_Account::getInstance()->retrieveNoIndependentByUserIdAndMajorThanDate($user->id, "1357016301");
//      var_dump("1357016401");
//      var_dump($accounts);
    }


}

