<?php

class Contabilidad_Services_Transaction extends Contabilidad_Services_Abstract {
    const NOT_ALL_PARAMS = "not all params";
    const NOT_AUTHENTICATED = "not authenticated";
    const UNSELECTED_TRANSACTION = "Unselected transaction";
    
    public function createTransaction($params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if($this->reviewParam('name', $params) && $this->reviewParam('value', $params)
        && $this->reviewParam('date', $params) && $this->reviewParam('id_account', $params)){
            $user = Contabilidad_Auth::getInstance()->getUser();
            if ($user->id){
                $account = Proxy_Account::getInstance()->findById($params['id_account']);
                $transaction = Proxy_Transaction::getInstance()->createNew($account,$params);
                $serialized = array ('id'=>$transaction->id , 'name'=>$transaction->name , 'value'=>$transaction->value , 
                    'date'=>$transaction->date , 'id_transaction_type'=> $transaction->id_transaction_type , 
                    'id_account'=>$transaction->id_account , 'comment'=>$transaction->comment , 
                    'is_frequent'=>$transaction->is_frequent , 'frequency_days'=>$transaction->frequency_days ,
                    'creation_date'=>$transaction->creation_date);
                $resp["accountUrl"] = Proxy_Account::getUrl_($account);
                $resp["transaction"] = $serialized;
                $resp["result"] = "success";
                $resp["reason"] = "OK";
            } $resp["reason"] = self::NOT_AUTHENTICATED;
        } return $resp;
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
                $resp["reason"] = "not authorized usser";
            }
        }
        return $resp;
    }
}
?>
