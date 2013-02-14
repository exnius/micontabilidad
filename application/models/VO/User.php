<?php
class VO_User extends Zend_Db_Table_Row {
    
    public function getPictureUrl(){
        if($this->picture_url && strlen($this->picture_url)){
            $url = $this->picture_url;
        } elseif($this->google_picture_url && strlen($this->google_picture_url)) {
            $url = $this->google_picture_url;
        } elseif($this->facebook_picture_url && strlen($this->facebook_picture_url)) {
            $url = $this->facebook_picture_url;
        } elseif ($this->gender == "female"){
            $url = LINKS_URL . "/avatars/default_female.png";
        } else {
            $url = LINKS_URL . "/avatars/default_male.png";
        }
        return $url;
    }
    
    public function getRecoverPassUrl(){
        return LINKS_URL . "/session/setpass?token=" . $this->token . "&email=" . $this->email;
    }
}