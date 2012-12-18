<?php
class Proxy_Currency extends Contabilidad_Proxy
{
    
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('currency', 'VO_Currency');
        }
        return (self::$_instance);
    }
    
    public function findById($currencyId) {
        return $this->getTable()->fetchRow("id = '$currencyId'");
    }
    
    public function retrieveCurrencys() {
        return $this->getTable()->fetchAll();
    }
}
?>
