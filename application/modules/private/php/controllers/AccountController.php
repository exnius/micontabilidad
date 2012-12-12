<?php

class Private_AccountController extends Zend_Controller_Action
{
    public function indexAction(){
        $this->view->pru="all";
    }
    
    public function findAction(){
        $this->view->pru="find";    
    }
    
    public function removeAction(){
        $this->view->pru="remove";    
    }
    
    public function editAction(){
        $this->view->pru="edit";    
    }
    
    public function addAction(){
        $this->view->pru="add";    
    }
}


?>
