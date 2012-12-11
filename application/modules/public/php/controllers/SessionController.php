<?php

class SessionControler extends Zend_Controller_Action
{
    
     public function registerAction()  
    {
         $this->view->julian="maestro rochy";
    }
    
    public function loginAction()
    {
        $this->view->clow="perro";
    }
    
    public function recoverpassAction()
    {
        $this->view->loco="helman";
    }
    
    public function setpassAction()
    {
        $this->view->popo="caca";
    }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
