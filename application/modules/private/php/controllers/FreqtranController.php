<?php

class Private_FreqtranController extends Zend_Controller_Action
{
    public function indexAction(){
        $userId = Contabilidad_Auth::getInstance()->getUser()->id;
        $transactions = Proxy_FreqTran::getInstance()->retrieveAllByUserId($userId);
        $this->view->transactions = $transactions;
        $serializedTrans = array();
        foreach($transactions as $tran){
            $serializedTrans[] = Proxy_Transaction::getInstance()->serializer($tran);
        }
        $this->view->serializedTransactions = $serializedTrans;
    }
}
?>
