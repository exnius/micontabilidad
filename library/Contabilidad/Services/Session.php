<?php
class Contabilidad_Services_Session extends Contabilidad_Services_Abstract {
    
    const USER_NOT_FOUND = "wrong authentication";
    const NOT_ALL_PARAMS = "not all params";
    const EMAIL_ALREADY_REGISTERED = "email already registered";

    public function login($params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if($this->reviewParam('email', $params) && $this->reviewParam('password', $params)){
            $params['password'] = Contabilidad_Auth::encryptPassword($params['email'], $params['password']);
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
    
    public function register($params){
        $puser = Proxy_User::getInstance();
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if($this->reviewParam('full_name', $params) && $this->reviewParam('email', $params) 
           && $this->reviewParam('password', $params) && $this->reviewParam('confirm_password', $params)){
            $user = $puser->findByEmail($params['email']);
            if($user){
                $resp["result"] = "failure";
                $resp["reason"] = self::EMAIL_ALREADY_REGISTERED;
            } else {
                $user = $puser->createNew($params);
                Contabilidad_Auth::getInstance()->login($params);
                $resp["result"] = "success";
                $resp["reason"] = "OK";
            }
        }
        return $resp;
    }
    
    public function recoverPassword($params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if($this->reviewParam('email', $params)){
            $user = Proxy_User::getInstance()->findByEmail($params['email']);
            if($user){
                $resp["result"] = "success";
                $resp["reason"] = "OK";
            } else {
                $resp["result"] = "failure";
                $resp["reason"] = self::USER_NOT_FOUND;
            }
        }
        return $resp;
    }
    
    public function connectByGoogle($params){
        $puser = Proxy_User::getInstance();
        
        //1. first find by google id
        $user = $puser->findByGoogleId($params['id']);
        //2. if user not found, find by email
        if(!$user){
            $user = $puser->findByEmail($params['email']);
            if(!$user){//2.1 register if user not found
                $user = $puser->createGoogleUser($params);
            } else {
                $puser->addGoogleData($user, $params);
            }
        }
        
        //3. login
        Contabilidad_Auth::getInstance()->loginByUser($user);
    }
}

