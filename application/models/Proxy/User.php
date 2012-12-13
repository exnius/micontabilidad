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
        $row = $this->createRow();
        $row->full_name = $params['full_name'];
        $row->password = $params['password'];
        $row->email = $params['email'];
        $row->id_currency = 1;
        $row->nickname = $this->createNickname($row->full_name);
        $row->save();
    }
    
    public function findByEmail($email){
        return $this->getTable()->fetchRow("email = '$email'");
    }
    
    public function createNickname ($nickname){
        
        $nickname = trim ($nickname);
        $nickname = preg_replace('/(À|Á|Â|Ã|Ä|Å|à|á|â|ã|ä|å|@)/','a',$nickname);
        $nickname = preg_replace('/(È|É|Ê|Ë|è|é|ê|ë)/','e',$nickname);
        $nickname = preg_replace('/(Ì|Í|Î|Ï|ì|í|î|ï)/','i',$nickname);
        $nickname = preg_replace('/(Ò|Ó|Ô|Õ|Ö|Ø|ò|ó|ô|õ|ö|ø)/','o',$nickname);
        $nickname = preg_replace('/(Ù|Ú|Û|Ü|ù|ú|û|ü)/','u',$nickname);
        $nickname = preg_replace('/(Ç|ç)/','c',$nickname);
        $nickname = preg_replace('/(Ñ|ñ)/','n',$nickname);
        $nickname = preg_replace('/(ÿ|Ý)/','y',$nickname);
        $nickname = preg_replace('/(\^)/',' ',$nickname);
        $nickname = strtolower ($nickname);
        $nickname = preg_replace('/\s+/',' ', $nickname);
        $nickname = preg_replace("[ ]",".",$nickname);
        
	    return $nickname;
	}
    
    private function findByNickname ($nickname){
        
    }

    
}