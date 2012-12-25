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
    
     public function createNew($account,$params){
        $row = $this->createRow();
        $row->name = $params['name'];
        $row->value = $params['value'];
        $row->date = isset($params['date']) ? $params['date'] : time();
        $row->comment = isset($params['comment']) ? $params['comment'] : "";
        $row->is_frequent = isset($params['is_frequent']) ? $params['is_frequent'] : 0;
        $row->frequency_days = isset($params['frequency_days']) ? $params['frequency_days'] : 0;
        $row->creation_date = time();
        $row->id_account = $account->id;
        $row->id_category_type = isset($params['id_category_type']) ? $params['id_category_type'] : 9; // default id 9 = other
        $row->id_transaction_type = $params['id_transaction_type'];
        $row->save();
        return $row;
    }
    
    public function findById ($transactionId){
        return $this->getTable()->fetchRow("id = '$transactionId'");
    }

    public function retrieveByAccountId($accountid, $order = "date DESC"){
        return $this->getTable()->fetchAll("id_account = '$accountid'", $order);
    }
    
    public function serializer ($transaction){
        return $serialized = array("id" => $transaction->id, 
            "transactionUrl" => "http://www.google.com",//$this->createUrl("transaction", $transaction),
            "name" => $transaction->name,"timestampDate" => $transaction->date,
            "date" => Contabilidad_Utils_Dates::toDate($transaction->date),"value" => $transaction->value,
            "dateClass" => $transaction->date > time() ? "@" : "",
            "transactionType" => $transaction->id_transaction_type == 1 ? "income" : "expense");
    }
}