<?php
class Proxy_WaitingEmail extends Contabilidad_Proxy
{
    
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('waiting_email', 'VO_WaitingEmail');
        }
        return (self::$_instance);
    }
    
    public function createNew($params){
        $row = $this->createRow();
        $row->id_user = $params['userId'];
        $row->template = $params['template'];
        $row->extra = isset($params['extra']) ? $params['extra'] : null;
        $row->save();
        return $row;
    }
    
    public function findById($id){
        return $this->getTable()->fetchRow("id='$id'");
    }
}