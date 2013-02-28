<?php

class Private_AccountController extends Zend_Controller_Action
{
    public function indexAction(){
        $request = $this->getRequest();
//        $explode = explode("-", $request->getParam('xr'));
//        $accountId = $explode[0];
        $accountId = $request->getParam('id');
        $user = Contabilidad_Auth::getInstance()->getUser();
        $this->view->account = $account = Proxy_Account::getInstance()->findById($accountId);
        if(!$account || $user->id != $account->id_user){
            $this->_forward("unavailable", "error", "public");
        } else {
            $this->view->serializedAccount = Proxy_Account::getInstance()->serializer($this->view->account);
            $transactions = Proxy_Transaction::getInstance()->retrieveBetweenByAccount($this->view->account);
            $this->view->outsideTrans = Proxy_Transaction::getInstance()->retrieveOutsideByAccount($this->view->account);
            $this->view->transactions = $transactions;
            $this->view->count = count($transactions);
            $serializedTrans = array();
            foreach($transactions as $tran){
                $serializedTrans[$tran->id] = Proxy_Transaction::getInstance()->serializer($tran);
            }
            foreach($this->view->outsideTrans as $tran){
                $serializedTrans[$tran->id] = Proxy_Transaction::getInstance()->serializer($tran);
            }
            $this->view->quantup = Proxy_Quantup::getInstance()->findPredeterminedByUserId($user->id);
            $this->view->currentBenefit = $this->view->account->calculateBenefit(strtotime("now"));
            $this->view->serializedTransactions = $serializedTrans;
            $this->view->categories = Proxy_CategoryType::getInstance()->fetchAll();
            $this->view->currencys = Proxy_Currency::getInstance()->retrieveCurrencys();
        }
    }
    
    public function iframeAction(){
        $this->_helper->layout()->disableLayout();
    }
    
    public function uploadpictureAction(){
        $this->view->root = ROOT;
        $root = ROOT . "/public/quantups_pictures/";
        $resp = array();
        $uploadfile_temp = $_FILES['picture']['tmp_name'];
        $name = $this->getUniquePictureName();
        $ext = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
        $pictureName = $this->getUniquePictureName($ext);
        $uploadfile_name = $root . $_FILES['picture']['name'] = $pictureName;
        $file_info = getimagesize($uploadfile_temp);
        if($_FILES['picture']['size'] > 2000000){
            $resp["file_info"] = $file_info;
            $resp["size"] = $_FILES['picture']['size'];
            $resp["response"] = "failure";
        } elseif (is_uploaded_file($uploadfile_temp) && $file_info){
            move_uploaded_file($uploadfile_temp, $uploadfile_name);
            $resp["response"] = "success";
            $resp["url"] = LINKS_URL . "/quantups_pictures/" . $pictureName;
        } else {
            $resp["response"] = "failure";
        }
        
        $this->_helper->layout()->disableLayout();
        $this->view->response = json_encode($resp);
//        echo $this->view->render("uploadavatar.phtml");
     }
     
     private function getUniquePictureName($ext){
         $userId = Contabilidad_Auth::getInstance()->getUser()->id;
         do {
            $name = Contabilidad_Utils_String::createRandomString(8);
            $newName = $userId . "_" . $name . "_quantup_picture." . strtolower($ext);
         }while(file_exists($newName));
         return $newName;
     }
}
?>
