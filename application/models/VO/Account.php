<?php
class VO_Account extends Zend_Db_Table_Row {
    
    public function calculateBenefit(){
        $transactions = Proxy_Transaction::getInstance()->retrieveByAccountId($this->id);
        $income = $expense = 0;
        foreach ($transactions as $transaction){
            if ($transaction->id_transaction_type == '1'){
                $income = $income + $transaction->value;
            }
            else{
                $expense = $expense + $transaction->value;
            }
        }
        $benefit = $income - $expense;
        return $benefit;
    }
    
    public function delete() {
        $transactions = Proxy_Transaction::getInstance()->retrieveByAccountId($this->id);
        foreach ($transactions as $transaction){
            $transaction->delete();
        }
        return parent::delete();
    }
}
?>
