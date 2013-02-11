<?php
class VO_Account extends Zend_Db_Table_Row {
    
    public function calculateBenefit($time = null){
        $ar = array('id' => $this->id, 'date_ini' => $this->date_ini, 'date_end' => $this->date_end);
        if($time && $time < $this->date_end) $ar['date_end'] = $time;
        $transactions = Proxy_Transaction::getInstance()->retrieveBetweenDatesAndAccountId($ar);
        $income = $expense = 0;
        foreach ($transactions as $transaction){
//            if ($transaction->date >= $this->date_ini && $transaction->date <= $this->date_end){
                if ($transaction->id_transaction_type == '1'){//income
                    $income = $income + $transaction->value;
                }
                else{
                    $expense = $expense + $transaction->value;//expense
                }
//            }
        }
        $benefit = $income - $expense;
        return $benefit;
    }
    
    public function getPictureUrl(){
        if($this->picture_url && strlen($this->picture_url)){
            $url = $this->picture_url;
        } else {
            $url = LINKS_URL . "/quantups_picture/sbudget.png";
        }
        return $url;
    }
    
    public function delete() {
        $transactions = Proxy_Transaction::getInstance()->retrieveAllByAccount($this);
        foreach ($transactions as $transaction){
            $transaction->delete();
        }
        return parent::delete();
    }
}
?>
