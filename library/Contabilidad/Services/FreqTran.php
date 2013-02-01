<?php
class Contabilidad_Services_FreqTran extends Contabilidad_Services_Abstract {
    const NOT_AUTHENTICATED = "not authenticated";
    const NOT_ALL_PARAMS = "not all params";
    
    public function deleteFreqTran ($id){
        $resp = array("result" => "failure", "reason" => self::NOT_AUTHENTICATED);
        if ($id){
            $transaction = Proxy_FreqTran::getInstance()->findById($id);
            if ($transaction->id_user == Contabilidad_Auth::getInstance()->getUser()->id){
                $transaction->delete();
                $resp["result"] = "success";
                $resp["reason"] = "OK";
            } else {
                $resp["reason"] = "not authorized user";
            }
        }
        return $resp;
    }
    
    public function saveFreqTransaction($params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        $ptran = Proxy_FreqTran::getInstance();
        if($this->reviewParam('name', $params) 
           && $this->reviewParam('value', $params)
           && $this->reviewParam('id_transaction_type', $params)){
            $user = Contabilidad_Auth::getInstance()->getUser();
            if ($user->id){
                if($this->reviewParam('id', $params) && $params['id'] != 0){
                    $transaction = $ptran->findById($params['id']);
                    list($transaction, $deletedTransactions) = $ptran->edit($transaction, $params);
                    $resp["reason"] = "DELETED";
                    $resp["deleted_transactions"] = $deletedTransactions;
                } else {
                    $ptran->id_user = $user->id;
                    $transaction = $ptran->createNew($params);
                    $resp["reason"] = "CREATED";
                }
                $serializedTransaction = $ptran->serializer($transaction);
                $resp["transaction"] = $serializedTransaction;
                $resp["result"] = "success";
            } else {
                $resp["reason"] = self::NOT_AUTHENTICATED;
            }
        }
        return $resp;
    }
}
?>
