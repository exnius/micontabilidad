<?php
class VO_Account extends Zend_Db_Table_Row {
    
    public function calculateBenefit(){
        $transactions = Proxy_Transaction::getInstance()->retrieveByAccountId($this->id);
        $entry = $egress = 0;
        foreach ($transactions as $transaction){
            if ($transaction->id_transaction_type == '1'){
                $entry = $entry + $transaction->value;
            }
            else{
                $egress = $egress + $transaction->value;
            }
        }
        $benefit = $entry - $egress;
        return $benefit;
    }
}
?>
