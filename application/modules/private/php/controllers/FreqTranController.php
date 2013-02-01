<?php

class Private_FreqTranController extends Zend_Controller_Action
{
    public function indexAction(){
        $user = Contabilidad_Auth::getInstance()->getUser();
        $userId = $user->id;
        $transactions = Proxy_FreqTran::getInstance()->retrieveAllByUserId($userId);
        $this->view->transactions = $transactions;
        $serializedTrans = array();
        foreach($transactions as $tran){
            $serializedTrans[] = Proxy_Transaction::getInstance()->serializer($tran);
        }
        $this->view->serializedTransactions = $serializedTrans;
        $this->view->categories = Proxy_CategoryType::getInstance()->retrieveAll();
        $this->view->serializedUser = Proxy_User::getInstance()->serialize($user);
    }
}
?>
