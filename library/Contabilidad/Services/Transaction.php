<?php

class Contabilidad_Services_Transaction extends Contabilidad_Services_Abstract {
    const NOT_ALL_PARAMS = "not all params";
    const NOT_AUTHENTICATED = "not authenticated";
    const UNSELECTED_TRANSACTION = "Unselected transaction";
    
    public function createTransaction($params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if($this->reviewParam('name', $params) 
           && $this->reviewParam('value', $params)
           && $this->reviewParam('id_transaction_type', $params)
           && $this->reviewParam('id_account', $params)){
            $user = Contabilidad_Auth::getInstance()->getUser();
            if ($user->id){
                $account = Proxy_Account::getInstance()->findById($params['id_account']);
                $transaction = Proxy_Transaction::getInstance()->createNew($account,$params);
                $serialized = Proxy_Transaction::getInstance()->serializer($transaction);
                $resp["transaction"] = $serialized;
                $resp["result"] = "success";
                $resp["reason"] = "OK";
            } else {
                $resp["reason"] = self::NOT_AUTHENTICATED;
            }
        }
        return $resp;
    }
    
    public function deleteTransaction ($id){
        $resp = array("result" => "failure", "reason" => self::UNSELECTED_TRANSACTION);
        if ($id){
        $transaction = Proxy_Transaction::getInstance()->findById($id);
        $account = Proxy_Account::getInstance()->findById($transaction->id_account);
            if ($account->id_user == Contabilidad_Auth::getInstance()->getUser()->id){
                $transaction->delete();
                $resp["result"] = "success";
                $resp["reason"] = "OK";
            } else {
                $resp["reason"] = "not authorized user";
            }
        }
        return $resp;
    }
}
?>
