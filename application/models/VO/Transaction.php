<?php
class VO_Transaction extends Zend_Db_Table_Row {
    private $_acctra = null;
    
    public function __get($columnName) {
        if($columnName == "date"){
            try{
                $value = parent::__get($columnName);
            } catch (Zend_Db_Table_Row_Exception $e){
                $value = $this->getAccTra()->__get($columnName);
            }
        } else {
            $value = parent::__get($columnName);
        }
        return $value;
    }
    
    public function delete() {
        
        $this->getAccTra()->delete();
        parent::delete();
    }
    
    public function getAccTra(){
        if(!$this->_acctra){
            $this->_acctra = Proxy_AccTra::getInstance()->findByTransaction($this);
        }
        return $this->_acctra;
    }
}