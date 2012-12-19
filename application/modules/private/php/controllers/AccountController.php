<?php

class Private_AccountController extends Zend_Controller_Action
{
    public function indexAction(){
        
    }
    
    public function findAction(){
        $this->view->pru="find";
    }
    
    public function removeAction(){
        $this-> view->pru="remove";
        $prus = Proxy_Account::getInstance()->retrieveByUserId("2");
        foreach ($prus as $pru){
            var_dump($pru->name);
            var_dump($pru->id);
        }
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
