<?php

class Private_IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function homeAction()
    {
      $this->view->pru="home";
    }


}

