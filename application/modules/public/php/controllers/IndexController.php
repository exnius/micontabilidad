<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function homeAction()
    {
//        var_dump("heheheh");
        // action body
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

