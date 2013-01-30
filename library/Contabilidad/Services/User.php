<?php
class Contabilidad_Services_User extends Contabilidad_Services_Abstract {
    const NOT_AUTHENTICATED = "not authenticated";
    const NOT_ALL_PARAMS = "not all params";
    const WRONG_PASSWORD = "WRONG_PASSWORD";
    
    public function editUser ($id, $params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if ($id){
            $puser = Proxy_User::getInstance();
            $user = $puser->findById($id);
            if($user){
                $user = $puser->edit($user, $params);
                $resp["user"] = $puser->serialize($user);
                $resp["result"] = "success";
                $resp["reason"] = "OK";
            } else {
                $resp["reason"] = "user not found";
            }
        }
        return $resp;
    }
    
    public function editPassword ($id, $params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if ($id && $this->reviewParam('old_pass', $params) && $this->reviewParam('new_pass', $params)){
            $puser = Proxy_User::getInstance();
            $user = $puser->findById($id);
            if($user){
                $oldPassEncrypted = Contabilidad_Auth::encryptPassword($user->email, $params["old_pass"]);
                if($oldPassEncrypted == $user->password){
                    $user = $puser->editPassword($user, $params['new_pass']);
                    Contabilidad_Auth::getInstance()->logout();
                    Contabilidad_Auth::getInstance()->loginByUser($user);
                    $resp["result"] = "success";
                    $resp["reason"] = "OK";
                } else {
                    $resp["reason"] = self::WRONG_PASSWORD;
                }
            } else {
                $resp["reason"] = "user not found";
            }
        }
        return $resp;
    }
}
?>
