<?php
class Proxy_CategoryType extends Contabilidad_Proxy
{
    
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('tipo_categoria', 'VO_CategoryType');
        }
        return (self::$_instance);
    }
}
?>
