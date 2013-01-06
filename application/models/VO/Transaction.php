<?php
class VO_Transaction extends Zend_Db_Table_Row {
    private $_acctra = null;
    
    public function __get($columnName) {
        if($columnName == "date"){
            try{
                $value = parent::__get($columnName);
            } catch (Zend_Db_Table_Row_Exception $e){
                if(!$this->_acctra){
                    $this->_acctra = Proxy_AccTra::getInstance()->findByTransaction($this);
                }
                $value = $this->_acctra->__get($columnName);
            }
        } else {
            $value = parent::__get($columnName);
        }
        return $value;
    }
    
    public function delete() {
        if(!$this->_acctra){
            $this->_acctra = Proxy_AccTra::getInstance()->findByTransaction($this);
        }
        $this->_acctra->delete();
        parent::delete();
    }
}