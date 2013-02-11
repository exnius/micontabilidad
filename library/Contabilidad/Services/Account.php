<?php
class Contabilidad_Services_Account extends Contabilidad_Services_Abstract {
    const NOT_ALL_PARAMS = "not all params";
    const NOT_AUTHENTICATED = "not authenticated";
    const UNSELECTED_ACCOUNT = "unselected account";
    const NOT_AUTHORIZED = "not authorized user";

    public function createAccount($params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if($this->reviewParam('date_end', $params) 
        && $this->reviewParam('date_ini', $params)
        && $this->reviewParam('name', $params)
        && $this->reviewParam('id_quantup', $params)
        && $this->reviewParam('id_currency', $params)){
           $user = Contabilidad_Auth::getInstance()->getUser();
           if ($user){
               $quantup = Proxy_Quantup::getInstance()->findById($params['id_quantup']);
               if($quantup->id_user == $user->id){
                   $account = Proxy_Account::getInstance()->createNew($user, $quantup, $params);
                   $serialized = Proxy_Account::getInstance()->serializer($account);
                   $resp["account"] = $serialized;
                   $resp["result"] = "success";
                   $resp["reason"] = "OK";
               } else {
                   $resp["reason"] = self::NOT_AUTHORIZED;
               }
           }  else {
               $resp["reason"] = self::NOT_AUTHENTICATED;
           }
        }
        return $resp;
    }
    
    
    
    public function editAccount ($params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if($this->reviewParam('date_end', $params) 
        && $this->reviewParam('date_ini', $params)
        && $this->reviewParam('name', $params)
        && $this->reviewParam('id_currency', $params)){
            $user = Contabilidad_Auth::getInstance()->getUser();
            if ($user){
                $account = Proxy_Account::getInstance()->findById($params['id']);
                if($account->id_user == $user->id){
                    $account = Proxy_Account::getInstance()->editAccount($account, $params);
                    $serialized = Proxy_Account::getInstance()->serializer($account);
                    $resp["account"] = $serialized;
                    $resp["result"] = "success";
                    $resp["reason"] = "OK";
                } else {
                    $resp["reason"] = self::NOT_AUTHORIZED;
                }
            } else {
                $resp["reason"] = self::NOT_AUTHENTICATED;
            }
        }
        return $resp;
    }
    
    public function deleteAccount ($id){
        
        $resp = array("result" => "failure", "reason" => self::UNSELECTED_ACCOUNT);
        if ($id){
        $account = Proxy_Account::getInstance()->findById($id);
            if ($account->id_user == Contabilidad_Auth::getInstance()->getUser()->id){
                $account->delete();
                $resp["result"] = "success";
                $resp["reason"] = "OK";
            } else {
                $resp["reason"] = "not autthorized usser";
            }
        }
        return $resp;
    }
    
    public function getAccountById ($id){
        $account = Proxy_Account::getInstance()->findById($id);
        return Proxy_Account::getInstance()->serializer($account);
    }
}

