<?php
class Proxy_Transaction extends Contabilidad_Proxy
{
    
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('transaction', 'VO_Transaction');
        }
        return (self::$_instance);
    }
    
     public function createTransaction($account,$params){
                $row = $this->createRow();
                $row->name = $params['name'];
                $row->value = $params['value'];
                $row->date = $params['date'];
                $row->comment = $params['comment'];
                $row->is_frequent = $params['is_frequent'];
                $row->frequency_days = $params['frequency_days'];
                $row->creation_date = time();
                $row->id_account = $account->id;
                $row->id_category_type = $params['id_category_type'];
                $row->id_transaction_type = $params['id_transaction_type'];
                $row->save();

    }
    
    public function findById ($transactionId){
        return $this->getTable()->fetchRow("id = '$transactionId'");
    }

    public function retrieveByAccountId($accountid){
        return $this->getTable()->fetchAll("id_account = '$accountid'");
    }
}