<?php

class Private_TransactionController extends Zend_Controller_Action
{
    public function allAction(){
        $this->view->pru="all";
    }
    
    public function addAction(){
        $this-> view->pru="add";
        $accounts =  Proxy_Account::getInstance()->retrieveAccountsByUserId('2');
        foreach ($accounts as $account){
            if($account->id=='2'){
                break;
            }
        }
        var_dump($account);
        $array = array('name'=>'Enero' , 'value'=>'123456' , 'date'=>'1234567890' , 'comment'=>'lo del motel',
                'is_frequent'=>true , 'frequency_days'=>'28' ,'cration_date'=>'1234567890' ,
                'id_category_type'=>'2' , 'id_transaction_type'=>'2' );
            Proxy_Transaction::getInstance()->createTransaction($account,$array);
    }
    
    public function editAction(){
        $this-> view->pru="edit";
    }
    public function removeAction(){
        $this-> view->pru="remove";
    }
}
?>
