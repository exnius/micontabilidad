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
    
     public function createNew($account, $params){
        $row = $this->createRow();
        $row->name = $params['name'];
        $row->value = $params['value'];
        $date = isset($params['date']) ? $params['date'] : time();
        if($date < $account->date_ini){
            $date = $account->date_ini;
        } elseif($date > $account->date_end){
            $date = $account->date_end;
        }
        $row->date = $date;
        $row->comment = isset($params['comment']) ? $params['comment'] : null;
        $row->is_frequent = isset($params['is_frequent']) ? $params['is_frequent'] : null;
        $row->frequency_days = isset($params['frequency_days']) ? $params['frequency_days'] : null;
        $row->frequency_time = isset($params['frequency_time']) ? $params['frequency_time'] : null;
        $row->creation_date = time();
        $row->id_account = $account->id;
        $row->id_category_type = isset($params['id_category_type']) ? $params['id_category_type'] : 9; // default id 9 = other
        $row->id_transaction_type = $params['id_transaction_type'];
        $row->save();
        $benefit = $account->calculateBenefit();
        $account->benefit = $benefit;
        $account->save();
        return $row;
    }
    
    public function findById ($transactionId){
        return $this->getTable()->fetchRow("id = '$transactionId'");
    }

    public function retrieveAllByAccount($account, $order = "date DESC"){
        $select = $this->getTable()->select()
                       ->where("id_account = '$account->id'")
                       ->order($order);
        return $this->getTable()->fetchAll($select);
    }
    
    public function retrieveBetweenByAccount($account, $order = "date DESC"){
        $select = $this->getTable()->select()
                       ->where("id_account = '$account->id'")
                       ->where("date >= '$account->date_ini'")
                       ->where("date <= '$account->date_end'")
                       ->order($order);
        return $this->getTable()->fetchAll($select);
    }
    
    public function retrieveOutsideByAccount($account, $order = "date DESC"){
        $select = $this->getTable()->select()
                       ->where("id_account = '$account->id'")
                       ->where("date < '$account->date_ini'")
                       ->orWhere("date > '$account->date_end'")
                       ->order($order);
        return $this->getTable()->fetchAll($select);
    }
    
     /*
     * Create URL from VO_Account
     * 
     * @return string
     * @params VO_Account
     */
    public static function getUrl_ ($transaction){
        $url = BASE_URL . "/private/transaction/index?id=" . $transaction->id;
        return $url;
    }
    
    public function serializer ($transaction){
        return $serialized = array("id" => $transaction->id, 
            "transactionUrl" => $this->getUrl_($transaction),
            "name" => $transaction->name,"timestampDate" => $transaction->date,
            "date" => Contabilidad_Utils_Dates::toDate($transaction->date),"value" => $transaction->value,
            "dateClass" => $transaction->date > time() ? "@" : "",
            "transactionType" => $transaction->id_transaction_type == 1 ? "income" : "expense");
    }
}