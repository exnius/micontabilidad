<?php
class VO_User extends Zend_Db_Table_Row {
    
    public function getPictureUrl(){
        if($this->picture_url && strlen($this->picture_url)){
            $url = $this->picture_url;
        } elseif($this->google_picture_url && strlen($this->google_picture_url)) {
            $url = $this->google_picture_url;
        } elseif($this->facebook_picture_url && strlen($this->facebook_picture_url)) {
            $url = $this->facebook_picture_url;
        } else {
            $url = "http://img.uefa.com/imgml/TP/players/14/2013/324x324/250011928.jpg";
        }
        return $url;
    }
    
    public function getRecoverPassUrl(){
        return BASE_URL . "/public/session/setpass?token=" . $this->token . "&email=" . $this->email;
    }
}