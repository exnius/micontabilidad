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
                $transactions = Proxy_Transaction::getInstance()->createNew($account, $params);
                $serializedTransactions = array();
                foreach($transactions as $transaction){
                    $serializedTransactions[] = Proxy_Transaction::getInstance()->serializer($transaction);
                }
                $serializedAccount = Proxy_Account::getInstance()->serializer($account);
                $resp["account"] = $serializedAccount;
                $resp["transactions"] = $serializedTransactions;
                $resp["result"] = "success";
                $resp["reason"] = "OK";
            } else {
                $resp["reason"] = self::NOT_AUTHENTICATED;
            }
        }
        return $resp;
    }
    
    public function deleteTransaction ($id, $accountId){
        $resp = array("result" => "failure", "reason" => self::UNSELECTED_TRANSACTION);
        if ($id){
            $transaction = Proxy_Transaction::getInstance()->findById($id);
            $account = Proxy_Account::getInstance()->findById($transaction->id_account);
            if ($account->id_user == Contabilidad_Auth::getInstance()->getUser()->id){
                $transaction->delete();
                $benefit = $account->calculateBenefit();
                $account->benefit = $benefit;
                $account->save();
                $serializedAccount = Proxy_Account::getInstance()->serializer($account);
                $resp["account"] = $serializedAccount;
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
