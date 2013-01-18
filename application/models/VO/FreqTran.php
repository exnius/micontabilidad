<?php
class VO_FreqTran extends Zend_Db_Table_Row {
    private $_children = array();
    public function delete(){
        $transactions = Proxy_Transaction::getInstance()->retrieveAllByFreqTranId($this->id);
        foreach($transactions as $tran){
            $tran->is_frequent = null;
            $tran->id_freq_tran = 0;
            $tran->frequency_days = null;
            $tran->frequency_time = null;
            $tran->save();
            $this->_children[] = $tran;
        }
        
        return parent::delete();
    }
    
    public function getChildren(){
        return $this->_children;
    }
}