<?php

class Private_ProfileController extends Zend_Controller_Action
{
    public function indexAction(){
        $user = Contabilidad_Auth::getInstance()->getUser();
        $this->view->user = $user;
        $currencys = Proxy_Currency::getInstance()->retrieveCurrencys();
        $this->view->currencys = $currencys;
        $this->view->countries = Contabilidad_Utils_Countries::getAll();
        $this->view->serializedUser = Proxy_User::getInstance()->serialize($user);
    }
    
    public function uploadavatarAction(){
        $this->view->root = ROOT;
        $root = ROOT . "/public/avatars/";
        $resp = array();
        $uploadfile_temp = $_FILES['avatar']['tmp_name'];
        $userId = Contabilidad_Auth::getInstance()->getUser()->id;
        $picture = $userId . '_avatar.jpg';
        $uploadfile_name = $root . $_FILES['avatar']['name'] = $picture;
        $file_info = getimagesize($uploadfile_temp);
        if($_FILES['avatar']['size'] > 2000000){
            $resp["file_info"] = $file_info;
            $resp["size"] = $_FILES['avatar']['size'];
            $resp["response"] = "failure";
        } elseif (is_uploaded_file($uploadfile_temp) && $file_info){
            move_uploaded_file($uploadfile_temp, $uploadfile_name);
            $resp["response"] = "success";
            $resp["url"] = BASE_URL . "/avatars/" . $picture;
            $user = Contabilidad_Auth::getInstance()->getUser();
            Proxy_User::getInstance()->addAvatarUrl($user, $resp["url"]);
        } else {
            $resp["response"] = "failure";
        }
        
        $this->_helper->layout()->disableLayout();
        $this->view->response = json_encode($resp);
//        echo $this->view->render("uploadavatar.phtml");
     }
     
     public function iframeAction(){
         $this->_helper->layout()->disableLayout();
     }
}
?>
