<?php
class Proxy_TransactionType extends Contabilidad_Proxy
{
    
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('transaction_type', 'VO_TransactionType');
        }
        return (self::$_instance);
    }
}
?>
