<?php
class Proxy_AccTra extends Contabilidad_Proxy
{
    
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('acc_tra', 'VO_AccTra');
        }
        return (self::$_instance);
    }
    
     public function createNew($accountId, $transactionId){
        $row = $this->createRow();
        $row->id_account = $accountId;
        $row->id_transaction = $transactionId;
        $row->creation_date = time();
        $row->save();
        return $row;
    }
    
}