<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function indexAction()
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
    
    public function blogAction()
    {
        $this->view->blog="blog";
    }
    
    public function joinAction()
    {
        $this->view->join="join";
    }
    
}

