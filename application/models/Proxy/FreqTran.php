<?php
class Proxy_FreqTran extends Contabilidad_Proxy
{
    
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('freq_tran', 'VO_FreqTran');
        }
        return (self::$_instance);
    }
    
    public function createNew($params, $account){
        $row = $this->createRow();
        $row = Proxy_Transaction::getInstance()->setParams($row, $params);
        $row->save();
        $transactions= Proxy_Transaction::getInstance()->createCopies($row, $account);
        
        return $transactions;
    }
    
    public function findById($id){
        return $this->getTable()->fetchRow("id='$id'");
    }
    
    public function retrieveAllByUserId($id, $order = "date DESC"){
        $select = $this->getTable()->select()
                       ->where("id_user = '$id'")
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
            "name" => $transaction->name,
            "timestampDate" => $transaction->date,
            "date" => Contabilidad_Utils_Dates::toDate($transaction->date),
            "value" => $transaction->value,
            "dateClass" => $transaction->date > time() ? "@" : "",
            "transactionType" => $transaction->id_transaction_type == 1 ? "income" : "expense");
    }
}