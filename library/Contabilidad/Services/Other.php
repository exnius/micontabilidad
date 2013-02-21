<?php
class Contabilidad_Services_Other extends Contabilidad_Services_Abstract {
    
    const USER_NOT_FOUND = "wrong authentication";
    const NOT_ALL_PARAMS = "not all params";
    const EMAIL_ALREADY_REGISTERED = "email already registered";
    
    public function sendFeedback($comment) {
        $user = Contabilidad_Auth::getInstance()->getUser();
        $extra = "user id: " . $user->id . '*-';
        $extra .= "email: " . $user->email . "*-";
        $extra .= "browser: " . Contabilidad_Utils_Browser::getBrowser() . "*-";
        $extra .= "plataforma: " . PHP_OS . "*-";
        $extra .= "comentario: " . $comment;
        $params = array("template" => "feedback", "userId" => $user->id, "extra" => $extra);
        Proxy_WaitingEmail::getInstance()->createNew($params);
        return null;
    }
}

