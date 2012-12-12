<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction()
    {
        $this->_forward("home");
    }

    public function homeAction()
    {
        
    }
    
    public function termsAction()  
    {
        $this->view->terms="terms";
    }

    public function aboutAction()
    {
        $this->view->about="about";
    }
    
}

