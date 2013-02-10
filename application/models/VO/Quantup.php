<?php
class VO_Quantup extends Zend_Db_Table_Row {
    
    public function getPictureUrl(){
        if($this->picture_url && strlen($this->picture_url)){
            $url = $this->picture_url;
        } else {
            $url = "http://img.uefa.com/imgml/TP/players/14/2013/324x324/250011928.jpg";
        }
        return $url;
    }
    
    public function delete() {
//        $transactions = Proxy_Transaction::getInstance()->retrieveAllByAccount($this);
//        foreach ($transactions as $transaction){
//            $transaction->delete();
//        }
        return parent::delete();
    }
}
?>
