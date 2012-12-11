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
            self::$_instance = new self('tipo_moneda', 'VO_Currency');
        }
        return (self::$_instance);
    }
}
?>
