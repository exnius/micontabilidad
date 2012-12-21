<?php

class Private_AccountController extends Zend_Controller_Action
{
    public function indexAction(){
        $request = $this->getRequest();
        $accountId = $request->getParam('id');
        $this->view->account = Proxy_Account::getInstance()->findById($accountId);
        $transactions = Proxy_Transaction::getInstance()->retrieveByAccountId($accountId,'date DESC');
        $this->view->transactions = $transactions;
        $this->view->count = count($transactions);
    }
    
    public function findAction(){
        $this->view->pru="find";
    }
    
    public function removeAction(){
        
    }
    
    public function editAction(){
        $this-> view->pru="edit";
        $user =  Proxy_User::getInstance()->findById('1');
        $array = array('name'=>'Febrero' , 'date_ini'=>'5888123456' , 'date_end'=>'58894567890' , 'benefit'=>'3000000');
        Proxy_Account::getInstance()->createNew($user,$array);
    }
    
    public function addAction(){
        $account = Proxy_Account::getInstance()->findById('1');
        $this->view->pru=$account->calculateBenefit();    
    }
}


?>
