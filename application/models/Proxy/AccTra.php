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
    
     public function createNew($accountId, $transactionId, $date){
        $row = $this->createRow();
        $row->id_account = $accountId;
        $row->id_transaction = $transactionId;
        $row->creation_date = time();
        $row->date = $date;
        $row->save();
        return $row;
     }
     
     public function findByTransaction($transaction){
         $select = $this->getTable()->select()
                   ->where("id_transaction = '$transaction->id'")
                   ->where("id_account = '$transaction->id_account'");
         return $this->getTable()->fetchRow($select);
     }
    
}