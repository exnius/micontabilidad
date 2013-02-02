<?php
class Proxy_User extends Contabilidad_Proxy
{
    
    protected static $_instance = null;

    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('user', 'VO_User');
        }
        return (self::$_instance);
    }
    
    public function createNew($params){
        $is = $this->checkEmail($params['email']);
        if ($is){
            $row = $this->createRow();
            $row->full_name = $params['full_name'];
            $row->email = $params['email'];
            $row->password = Contabilidad_Auth::encryptPassword($params['email'], $params['password']);
            $row->id_currency = 1;
            $row->nickname = $this->createNickname($params['full_name']);
            $row->creation_date = time();
            $row->registered_by = "email";
            $row->save();
        }
        else
            Contabilidad_Exceptions::showException ('Este email ya existe');
    }
    
    public function createGoogleUser($params){
        $row = $this->createRow();
        $row->full_name = $params['name'];
        $row->email = $params['email'];
        $row->id_currency = 1;
        $row->nickname = $this->createNickname($params['name']);
        $row->creation_date = time();
        $row->google_id = $params['id'];
        if(isset($params['picture'])){
            $row->google_picture_url = $params['picture'];
        }
        if (isset($params ['gender'])){
            $row->gender = $params['gender'];
        }
        if (isset($params ['locale'])){
            $row->locale = $params['locale'];
        }
        $password = Contabilidad_Utils_String::createRandomString();
        $encryptedPass = Contabilidad_Auth::encryptPassword($row->email, $password);
        $row->token = Contabilidad_Utils_String::createRandomString(20);
        $row->password = $encryptedPass;
        $row->registered_by = "google";
        $row->save();
        //send email to user
        $ar = array("userId" => $row->id, "template" => "welcome", "extra" => $password);
        Proxy_WaitingEmail::getInstance()->createNew($ar);
//        Contabilidad_Utils_EmailTemplate::getInstance()->sendWelcomeEmailAndPassword($row, $password);
        return $row;
    }
    
    public function createFacebookUser($params){
        $row = $this->createRow();
        $row->full_name = $params['full_name'];
        $row->email = $params['email'];
        $row->id_currency = 1;
        $row->nickname = $this->createNickname($params['full_name']);
        $row->creation_date = time();
        $row->facebook_id = $params['uid'];
        $row->facebook_picture_url = "https://graph.facebook.com/" . $params['uid'] . "/picture?type=large";
        $row->facebook_token = $this->extendFacebookToken($params['token']);
        if (isset($params ['gender'])){
            $row->gender = $params['gender'];
        }
        if(isset($params['current_location'])){
            if(isset($params['current_location']['country'])){
                $row->country = Contabilidad_Utils_Countries::getIdByName($params['current_location']['country']);
            }
            if ($row->country){
                $row->locale = Contabilidad_Utils_Countries::getLocationById($row->country);
            }
            if(isset($params['current_location']['city'])){
                $row->city = $params['current_location']['city'];
            }
        }
        $password = Contabilidad_Utils_String::createRandomString();
        $encryptedPass = Contabilidad_Auth::encryptPassword($row->email, $password);
        $row->token = Contabilidad_Utils_String::createRandomString(20);
        $row->password = $encryptedPass;
        $row->registered_by = "facebook";
        $row->save();
        //send email to user
        $ar = array("userId" => $row->id, "template" => "welcome", "extra" => $password);
        Proxy_WaitingEmail::getInstance()->createNew($ar);
        return $row;
    }
    //3125001921
    
    public function edit($user, $params){
        foreach($params as $prp => $value){
            if($prp == "email") continue;
            $user->__set($prp, $value);
        }
        $user->save();
        return $user;
    }
    
    public function addGoogleData($user, $params){
        $user->google_id = $params ['id'];
        if (isset($params ['picture'])){
                $user->google_picture_url = $params ['picture'];
        }
        if (isset($params ['gender'])){
                $user->gender = $params ['gender'];
        }
        if (isset($params ['locale'])){
                $user->locale = $params ['locale'];
        }
        $user->save();
        return $user;
    }
    
    public function addFacebookData($user, $params){
        $user->facebook_id = $params ['uid'];
        $user->facebook_token = $this->extendFacebookToken($params ['token']);
        $user->facebook_picture_url = "https://graph.facebook.com/" . $params['uid'] . "/picture?type=large";;
        if (isset($params ['gender'])){
            $row->gender = $params['gender'];
        }
        if(isset($params['current_location'])){
            if(isset($params['current_location']['country'])){
                $user->country = Contabilidad_Utils_Countries::getIdByName($params['current_location']['country']);
            }
            if ($user->country){
                $user->locale = Contabilidad_Utils_Countries::getLocationById($user->country);
            }
            if(isset($params['current_location']['city'])){
                $user->city = $params['current_location']['city'];
            }
        }
        $user->save();
        return $user;
    }

    public function editPassword($user, $password){
        $newPass = Contabilidad_Auth::encryptPassword($user->email, $password);
        $user->password = $newPass;
        $user->token = null;
        $user->save();
        return $user;
    }

    public function checkEmail($email){
        $mailCorrect = false;
        if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){
          if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) {
            if (substr_count($email,".")>= 1){
                $termDom = substr(strrchr ($email, '.'),1);
              if (strlen($termDom)>1 && strlen($termDom)<5 && (!strstr($termDom,"@")) ){
                  $beforeDom = substr($email,0,strlen($email) - strlen($termDom) - 1);
                  $characterUlt = substr($beforeDom,strlen($beforeDom)-1,1);
                if ($characterUlt != "@" && $characterUlt != "."){
                    $mailCorrect = true;
                   }
                 }
              }
            }
          }


        if ($mailCorrect){
            $isEmail= $this->findByEmail($email);
            if (!$isEmail){
                return true;
            } 
               else{
                    return false;
               } 
        }    
        else{
            return false;
        }   
        }               
    
    public function findById ($id){
        return $this->getTable()->fetchRow("id = '$id'");
    }
    
    public function findByGoogleId ($id){
        return $this->getTable()->fetchRow("google_id = '$id'");
    }
    
    public function findByFacebookId ($id){
        return $this->getTable()->fetchRow("facebook_id = '$id'");
    }

    public function findByEmail($email){
        return $this->getTable()->fetchRow("email = '$email'");
    }
    
    public function findByNickname ($nickname){
        return $this->getTable()->fetchRow("nickname = '$nickname'");
    }
    
    public function findByToken ($token){
        return $this->getTable()->fetchRow("token = '$token'");
    }

    private function createNickname ($nickname){
        $newNickname = Contabilidad_Utils_String::cleanString($nickname);
        $nickname = $newNickname;
        $suf =1;
        $ban=false;
        do{
            $is = $this->findByNickname($nickname);
            if (!$is){
                $ban = true;
            }else{
                $nickname = $newNickname . $suf;
                $suf++;
            }
        }while ($ban==false);
        return $nickname;
    }

    public function serialize($user){
        $array = array();
        $array["full_name"] = $user->full_name;
        $array["email"] = $user->email;
        $array["nickname"] = $user->nickname;
        $array["id"] = $user->id;
        $array["country"] = $user->country;
        $array["city"] = $user->city;
        $array["id_currency"] = $user->id_currency;
        $array["gender"] = $user->gender;
        $array["locale"] = $user->locale;
        $array["avatarUrl"] = $user->getPictureUrl();
        return $array;
    }
    
    
    public function addAvatarUrl($user, $url){
        //checkout it is an url
        $user->picture_url = $url;
        $user->save();
    }
    
    public function extendFacebookToken($token)
    {
        $conf = Zend_Registry::get('Config');
        $appId = $conf->oauth->facebook->clientId;
        $appSecret = $conf->oauth->facebook->secret;
        $token_url = "https://graph.facebook.com/oauth/access_token?" .
                     "client_id=$appId&" .
                     "client_secret=$appSecret&" .
                     "grant_type=fb_exchange_token&" .
                     "fb_exchange_token=" . $token;
        $client = new Zend_Http_Client($token_url);
        $client->setMethod(Zend_Http_Client::GET);
        $response = $client->request();
        $params = array();
        parse_str($response->getBody(), $params);
        return $params['access_token'];

    }
}