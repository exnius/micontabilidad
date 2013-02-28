<?php

class Private_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
      $user = Contabilidad_Auth::getInstance()->getUser();
      if($user->nickname != $this->_request->getParam("nickname")){
          $this->_redirect(BASE_URL . "/$user->nickname");
      }
      $this->view->accounts = Proxy_Account::getInstance()->retrieveByUserId($user->id);
      $this->view->currencys = Proxy_Currency::getInstance()->retrieveCurrencys();
      $this->view->user = $user;
      
      $pacc = Proxy_Account::getInstance();
      $accounts = $pacc->retrieveByUserId($user->id);
      
      $this->view->quantup = Proxy_Quantup::getInstance()->findPredeterminedByUserId($user->id);
      
      $serializedAccounts = array();
      foreach($accounts as $acc){
          $serializedAccounts[$acc->id] = $pacc->serializer($acc);
      }
      $this->view->serializedAccounts = $serializedAccounts;
    }


}

