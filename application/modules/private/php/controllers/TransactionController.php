<?php

class Private_TransactionController extends Zend_Controller_Action
{
    public function allAction(){
        $this->view->pru="all";
    }
    
    public function addAction(){
        $this-> view->pru="add";
    }
    
    public function editAction(){
        $this-> view->pru="edit";
    }
    public function removeAction(){
        $this-> view->pru="remove";
    }
}
?>
