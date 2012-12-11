<?php

class Private_TransactionController extends Zend_Controller_Action
{
    public function findAction(){
        $this-> view->pru="find";
    }
    
    public function editAction(){
        $this-> view->pru="edit";
    }
}
?>
