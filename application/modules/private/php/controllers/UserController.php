<?php

class Private_UserController extends Zend_Controller_Action
{
    public function findAction(){
        $this->view->pru="find";
    }
    
    public function editAction(){
        $this->view->pru="edit";
    }
}
?>
