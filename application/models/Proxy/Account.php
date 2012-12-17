<?php
class Proxy_Account extends Contabilidad_Proxy
{
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('account', 'VO_Account');
        }
        return (self::$_instance);
    }
    
    public function retrieveAccountsByUserId($userid){
        return $this->getTable()->fetchAll("id_user = '$userid'");
    }
}
?>
