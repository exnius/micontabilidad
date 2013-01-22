<?php
class Contabilidad_Services_FreqTran extends Contabilidad_Services_Abstract {
    const NOT_AUTHENTICATED = "not authenticated";
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
}
?>
