<?php
class Contabilidad_Services_Account extends Contabilidad_Services_Abstract {
    const NOT_ALL_PARAMS = "not all params";

    public function createAccount($params){
        $resp = array("result" => "failure", "reason" => self::NOT_ALL_PARAMS);
        if($this->reviewParam('date_end', $params) 
           && $this->reviewParam('date_ini', $params)
           && $this->reviewParam('name', $params)
           && $this->reviewParam('id_currency', $params)){
            //@TODO
        }
        return $resp;
    }
}

