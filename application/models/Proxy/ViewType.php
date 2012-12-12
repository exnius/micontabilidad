<?php
class Proxy_ViewType extends Contabilidad_Proxy
{
    
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('view_type', 'VO_ViewType');
        }
        return (self::$_instance);
    }
}