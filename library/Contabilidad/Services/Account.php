<?php
class Contabilidad_Services_Account extends Contabilidad_Services_Abstract {
    const NOT_ALL_PARAMS = "not all params";
    const NOT_AUTHENTICATED = "not authenticated";

    public function createAccount($params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if($this->reviewParam('date_end', $params) 
           && $this->reviewParam('date_ini', $params)
           && $this->reviewParam('name', $params)
           && $this->reviewParam('id_currency', $params)){
           if (Contabilidad_Auth::getInstance()->getUser()){
               $user = Contabilidad_Auth::getInstance()->getUser();
               Proxy_Account::getInstance()->createNew($user, $params);
               $resp["failure"] = "success";
               $resp["reason"] = "OK";
           }  else {
               $resp["reason"] = self::NOT_AUTHENTICATED;
           }
        }
        return $resp;
    }
}

