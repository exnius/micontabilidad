<?php
class Contabilidad_Services_User extends Contabilidad_Services_Abstract {
    const NOT_AUTHENTICATED = "not authenticated";
    const NOT_ALL_PARAMS = "not all params";
    
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
}
?>
