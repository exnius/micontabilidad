<?php
class VO_Transaction extends Zend_Db_Table_Row {
    public function delete() {
        if($this->is_frequent){
            $other = Proxy_Transaction::getInstance()->retrieveAllByFreqTranId($this->id_freq_tran);
            if($other->count() == 1){//remove freq_tran if there is no child left
                $freq = Proxy_FreqTran::getInstance()->findById($this->id_freq_tran);
                if($freq) $freq->delete();//it could be removed when the user edited it
            }
        }
        return parent::delete();
    }
}