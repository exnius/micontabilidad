<?php
class Proxy_Transaction extends Contabilidad_Proxy
{
    
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('transaction', 'VO_Transaction');
        }
        return (self::$_instance);
    }
    
     public function createNew($account, $params){
        $transactions = array();
        
        $date = isset($params['date']) ? $params['date'] : time();
        if($date < $account->date_ini){
            $date = $account->date_ini;
        } elseif($date > $account->date_end){
            $date = $account->date_end;
        }
        $params['date'] = $date;
        if(isset($params['is_frequent']) && $params['is_frequent']){
            $transactions = Proxy_FreqTran::getInstance()->createNew($params, $account);
            //create frequent transactions to old accounts
            $accounts = Proxy_Account::getInstance()->retrieveNoIndependentByUserIdAndMajorThanDate(Contabilidad_Auth::getInstance()->getUser()->id, $date);
            foreach($accounts as $acc){
                if($acc->id != $account->id){
                    $freqTran = Proxy_FreqTran::getInstance()->findById($transactions[0]->id_freq_tran);
                    $this->createFrequencyTransactions($acc, $freqTran);
                    $acc->benefit = $acc->calculateBenefit();
                    $acc->save();
                }
            }
        } else {
            $row = $this->createRow();
            $row = $this->setParams($row, $params);
            $row->id_account = $account->id;
            $row->save();
            $transactions[] = $row;
        }
        $account->benefit = $account->calculateBenefit();
        $account->save();
        
        return $transactions;
    }
    
    public function edit($tran, $params){
        $transactions = array();
        $isFrequent = $tran->is_frequent;
        $freqDays = $tran->frequency_days;
        $freqTime = $tran->frequency_time;
        $value = $tran->value;
        $name = $tran->name;
        foreach ($params as $prp => $val){
            if($prp == "precise_frequency_days") continue;
            $tran->$prp = $val;
        }
        if(!isset($params["is_frequent"]) || !$params["is_frequent"]){
            $tran->is_frequent = null;
            $tran->frequency_days = null;
            $tran->frequency_time = null;
        }
        $tran->save();
        $transactions[] = $tran;
        $account = Proxy_Account::getInstance()->findById($tran->id_account);
        if($isFrequent != $tran->is_frequent){
            if($tran->is_frequent){
                $params["omite_date"] = $tran->date;
                $transactions = Proxy_FreqTran::getInstance()->createNew($params, $account);
                $freqTran = Proxy_FreqTran::getInstance()->lastInsertByUserId(Contabilidad_Auth::getInstance()->getUser()->id);
                $tran->id_freq_tran = $freqTran->id;
                $tran->save();
                $transactions[] = $tran;
            } else {
                $freqTran = Proxy_FreqTran::getInstance()->findById($tran->id_freq_tran);
                $freqTran->delete();
                $transactions = $freqTran->getChildren();
                
            }
        } elseif($tran->is_frequent){
            //create copies + delete younger children tran
            $freqTran = Proxy_FreqTran::getInstance()->findById($tran->id_freq_tran);
            $freqTran->frequency_days = $tran->frequency_days;
            $freqTran->frequency_time = $tran->frequency_time;
            $freqTran->id_category_type = $tran->id_category_type;
            $freqTran->name = $tran->name;
            $freqTran->value = $tran->value;
            $freqTran->save();
            
            if($name != $tran->name || $value != $tran->value){
                $youngerChildren = $this->retrieveYoungerByFreqTranIdAndTransaction($id, $tran);
                foreach($youngerChildren as $ytran){
                    $ytran->name = $tran->name;
                    $ytran->value = $tran->value;
                    $ytran->save();
                }
            }
        }
        $account->benefit = $account->calculateBenefit();
        $account->save();
        return $transactions;
    }
    
    public function setParams($row, $params){
        $row->name = $params['name'];
        $row->value = $params['value'];
        $date = isset($params['date']) ? $params['date'] : time();
        $row->date = $date;
        $row->id_user = Contabilidad_Auth::getInstance()->getUser()->id;
        $row->comment = isset($params['comment']) ? $params['comment'] : null;
        $row->is_frequent = isset($params['is_frequent']) ? $params['is_frequent'] : null;
        $row->frequency_days = isset($params['frequency_days']) ? $params['frequency_days'] : null;
        $row->frequency_time = isset($params['frequency_time']) ? $params['frequency_time'] : null;
        $row->creation_date = time();
        $row->id_category_type = isset($params['id_category_type']) ? $params['id_category_type'] : 9; // default id 9 = other
        $row->id_transaction_type = $params['id_transaction_type'];
        return $row;
    }
    
    public function createCopies($tran, $account, $omiteDate){
        $transactions = array();
        $day = 60*60*24;
        $date = $tran->date;
        $max = 1388448000;// => 31/12/2013
        if($tran->frequency_time){
            $max = $tran->frequency_time*$tran->frequency_days*$day + $tran->date;
        }
        if($max > $account->date_ini && $date < $account->date_ini){//if date < date_ini and max is bigger than date_ini
            $diff = $tran->date - $account->date_ini;
            $date = $tran->date + $diff + $tran->frequency_days*$day;
        }
        while($date >= $account->date_ini && $date <= $account->date_end && $date < $max){
            if($date != $omiteDate){
                $row = $this->createRow();
                $row->name = $tran->name;
                $row->value = $tran->value;
                $row->id_user = $tran->id_user;
                $row->comment = $tran->comment;
                $row->is_frequent = $tran->is_frequent;
                $row->frequency_days = $tran->frequency_days;
                $row->frequency_time = $tran->frequency_time;
                $row->creation_date = time();
                $row->id_account = $account->id;
                $row->value = $tran->value;
                $row->id_category_type = $tran->id_category_type;
                $row->id_transaction_type = $tran->id_transaction_type;
                $row->date = $date;
                $row->id_freq_tran = $tran->id;
                $row->save();

                $transactions[] = $row;
            }
            $date = $date + $tran->frequency_days*$day;
        }
        return $transactions;
    }
    
    public function createAllFrequencyTransactions($account){
        $transactions = Proxy_FreqTran::getInstance()->retrieveAllByUserId($account->id_user);
        foreach($transactions as $tran){
            $this->createFrequencyTransactions($account, $tran);
        }
    }
    
    public function createFrequencyTransactions($account, $tran){
        $day = 60*60*24;
        //if frequency time is infinite, it will be a tran of current account
        $times = ceil(($account->date_ini - $tran->date)/($tran->frequency_days*$day));
        if(($tran->frequency_time == 0 || $times <= $tran->frequency_time) && $tran->date <= $account->date_ini){
            $newDate = $tran->date + ($tran->frequency_days*$day*$times);
            if($newDate <= $account->date_end){//if new date is between period, create tran
                $tran->date = $newDate;
                $transactions = Proxy_Transaction::getInstance()->createCopies($tran, $account);
            }
        }
        
    }
    
    public function findById($id){
        return $this->getTable()->fetchRow("id='$id'");
    }

    public function retrieveAllByAccount($account){
        $select = $this->getTable()->select()
                       ->where("id_account = '$account->id'");
        return $this->getTable()->fetchAll($select);
    }
    
    public function retrieveBetweenByAccount($account, $order = "date DESC"){
        $select = $this->getTable()->select()
                       ->where("id_account = '$account->id'")
                       ->where("date >= '$account->date_ini'")
                       ->where("date <= '$account->date_end'")
                       ->order($order);
        return $this->getTable()->fetchAll($select);
    }
    
    public function retrieveOutsideByAccount($account, $order = "date DESC"){
        $firstCondition = $this->getTable()->select()
                          ->where("date < '$account->date_ini'")
                          ->orWhere("date > '$account->date_end'")
                          ->getPart(Zend_Db_Select::WHERE);
        $select = $this->getTable()->select(Zend_Db_Select::WHERE)
                       ->where("id_account = '$account->id'")
                       ->where(implode(" ", $firstCondition))
                       ->order($order);
        
        return $this->getTable()->fetchAll($select);
    }
    
    public function retrieveAllByFreqTranId($id){
        return $this->getTable()->fetchAll("id_freq_tran='$id'");
    }
    
    public function retrieveYoungerByFreqTranIdAndTransaction($id, $tran){
        $select = $this->getTable()->select()
                       ->where("id_freq_tran = '$id'")
                       ->where("date > '$tran->date'");
        return $this->getTable()->fetchAll($select);
    }
    
    public function retrieveAllByUserId($id, $order = "date DESC"){
        
    }
    
     /*
     * Create URL from VO_Account
     * 
     * @return string
     * @params VO_Account
     */
    public static function getUrl_ ($transaction){
        $url = BASE_URL . "/private/transaction/index?id=" . $transaction->id;
        return $url;
    }
    
    public function serializer ($transaction){
        return $serialized = array(
            "id" => $transaction->id, 
            "transactionUrl" => $this->getUrl_($transaction),
            "name" => $transaction->name,
            "timestampDate" => $transaction->date,
            "date" => Contabilidad_Utils_Dates::toDate($transaction->date),
            "value" => $transaction->value,
            "id_category_type" => $transaction->id_category_type,
            "id_transaction_type" => $transaction->id_transaction_type,
            "is_frequent" => $transaction->is_frequent,
            "frequency_days" => $transaction->frequency_days,
            "frequency_time" => $transaction->frequency_time,
            "id_user" => $transaction->id_user,
            "dateClass" => $transaction->date > time() ? "@" : "",
            "transactionType" => $transaction->id_transaction_type == 1 ? "income" : "expense");
    }
}