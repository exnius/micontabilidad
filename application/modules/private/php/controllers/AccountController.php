<?php

class Private_AccountController extends Zend_Controller_Action
{
    public function indexAction(){
        $request = $this->getRequest();
        $accountId = $request->getParam('id');
        $this->view->account = Proxy_Account::getInstance()->findById($accountId);
        $this->view->serializedAccount = Proxy_Account::getInstance()->serializer($this->view->account);
        $transactions = Proxy_Transaction::getInstance()->retrieveBetweenByAccount($this->view->account);
        $this->view->outsideTrans = Proxy_Transaction::getInstance()->retrieveOutsideByAccount($this->view->account);
        $this->view->transactions = $transactions;
        $this->view->count = count($transactions);
        $this->view->categories = Proxy_CategoryType::getInstance()->fetchAll();
    }
    
    public function findAction(){
        $this->view->pru="find";
        $transactions = Proxy_Transaction::getInstance()->retrieveFrequentsByUserId(27);
        foreach($transactions as $transaction){
            var_dump($transaction->name);
        }
        exit();
    }
    
    public function removeAction(){
        $resp = (Contabilidad_Services_Transaction::deleteTransaction('9'));
        var_dump($resp);
    }
    
    public function editAction(){
        $this-> view->pru="edit";
        $user =  Proxy_User::getInstance()->findById('1');
        $array = array('name'=>'Febrero' , 'date_ini'=>'5888123456' , 'date_end'=>'58894567890' , 'benefit'=>'3000000');
        Proxy_Account::getInstance()->createNew($user,$array);
    }
    
    public function addAction(){
        
    }
}


?>
