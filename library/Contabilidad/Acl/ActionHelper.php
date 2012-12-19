<?php
/** Zend_Controller_Action_Helper_Abstract */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

class Contabilidad_Acl_ActionHelper extends Zend_Controller_Action_Helper_Abstract
{
	protected $_action;
	/**
	 * Enter description here...
	 *
	 * @var Zend_Auth
	 */
	protected $_root;
	protected $_controller;
	protected $_module;
	protected $auth;
	
	public function __construct($root)
	{
	    $this->auth = Zend_Auth::getInstance();
	    $this->_root = $root;
	}
	
	/**
	* Hook into action controller initialization
	* @return void
	*/
	public function init()
	{
		$this->_action = $this->getActionController();
		$this->_controller = $this->_action->getRequest()
			->getControllerName(); 
		$this->_module = $this->_action->getRequest()
			->getModuleName();
	}
	
    public function preDispatch()
    {
        $request = $this->_action->getRequest();
        $controller = $request->getControllerName();
        $action = $request->getActionName();
        $module = $request->getModuleName();
        $helper = $this->_action->getHelper("Redirector");

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
        $this->setViewParams($view);
        $view->isLogged = $this->auth->hasIdentity();
        $view->action = $action;
        $view->module = $module;
        $view->controller = $controller;
        if($module == "private"){
            Zend_Layout::startMvc(array('layoutPath' => $this->_root . '/application/views/scripts' , 'layout' => 'private-layout'));
        }
        if($view->isLogged) {
            if($module == "public" && $action != "logout" && $controller != "error" && $controller != "services"){
                $helper->direct("home", "index", "private");
            }
        } else {
            if($module == "private"){
                $helper->direct("index", "index", "public");
            }
        }
    }
    
    public function setViewParams($view){
        $config = Zend_Registry::get('Config');
        $view->gapi_id = $config->oauth->google->clientId;
        $view->gapi_secret = $config->oauth->google->secret;
    }
}