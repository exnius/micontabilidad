<?php
class VO_Transaction extends Zend_Db_Table_Row {
    public function delete() {
        if($this->is_frequent){
            $other = Proxy_Transaction::getInstance()->retrieveByFreqTranId($this->id_freq_tran);
            if(!$other){//remove freq_tran if there is no child left
                $freq = Proxy_FreqTran::getInstance()->findById($this->id_freq_tran);
                $freq->delete();
            }
        }
        return parent::delete();
    }
}