<?php
class VO_AccTra extends Zend_Db_Table_Row {
    private $_transaction = null;
    
    public function getTransaction(){
        if(!$this->_transaction){
            $this->_transaction = Proxy_Transaction::getInstance()->findById($this->id_transaction);
        }
        return $this->_transaction;
    }
    
    public function delete() {
        $tran = $this->getTransaction();
        $del = parent::delete();
//        var_dump($this->getTransaction());
        $accTra = Proxy_AccTra::getInstance()->findByTransaction($tran);
        if(!$accTra) $tran->delete();
        return $del;
    }
}