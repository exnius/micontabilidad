<?php
class Contabilidad_Services_Session extends Contabilidad_Services_Abstract {
    
    const USER_NOT_FOUND = "wrong authentication";
    const NOT_ALL_PARAMS = "not all params";

    public function login($params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if($this->reviewParam('email', $params) && $this->reviewParam('password', $params)){
            if(Contabilidad_Auth::getInstance()->login($params)){
                $resp["result"] = "success";
                $resp["reason"] = "OK";
            } else {
                $resp["result"] = "failure";
                $resp["reason"] = self::USER_NOT_FOUND;
            }
        }
        return $resp;
    }
    
    public function loginByGoogle($params){
        
    }
}

