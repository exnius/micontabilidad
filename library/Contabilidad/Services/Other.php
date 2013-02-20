<?php
class Contabilidad_Services_Other extends Contabilidad_Services_Abstract {
    
    const USER_NOT_FOUND = "wrong authentication";
    const NOT_ALL_PARAMS = "not all params";
    const EMAIL_ALREADY_REGISTERED = "email already registered";
    
    public function sendFeedback($comment) {
        $user = Contabilidad_Auth::getInstance()->getUser();
        $extra = "id: " . $user->id . "\n";
        $extra .= "email: " . $user->email . "\n";
        $extra .= "browser: " . Contabilidad_Utils_Browser::getBrowser() . "\n";
        $extra .= "plataforma: " . PHP_OS . "\n\n";
        $extra .= "comentario: " . $comment . "\n";
        $params = array("template" => "feedback", "userId" => $user->id, "extra" => $extra);
        Proxy_WaitingEmail::getInstance()->createNew($params);
        return null;
    }
}

