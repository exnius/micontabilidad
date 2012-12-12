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
        $row->save();
    }
    
    public function findByEmail($email){
        return $this->getTable()->fetchRow("email = '$email'");
    }
}