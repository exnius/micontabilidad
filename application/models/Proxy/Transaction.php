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
            $row->id_quantup = $account->id_quantup;
            $row->save();
            $transactions[] = $row;
        }
        $account->benefit = $account->calculateBenefit();
        $account->save();
        
        return $transactions;
    }
    
    //Return transactions and deleted transactions (if there are)
    public function edit($tran, $params){
        $transactions = array();
        $deletedTransactions = array();
        $isFrequent = $tran->is_frequent;
        $freqDays = $tran->frequency_days;
        $freqTime = $tran->frequency_time;
        $idCategoryType = $tran->id_category_type;
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
        if($isFrequent != $tran->is_frequent){//if the user changed "is_frequent"
            if($tran->is_frequent){//if it is frequent now
                //create freq tran and copies
                $params["omite_date"] = $tran->date;
                $transactions = Proxy_FreqTran::getInstance()->createNew($params, $account);
                $freqTran = Proxy_FreqTran::getInstance()->lastInsertByUserId(Contabilidad_Auth::getInstance()->getUser()->id);
                $tran->id_freq_tran = $freqTran->id;
                $tran->save();
                $transactions[] = $tran;
            } else {//if it is not frequent anymore
                //delete freqtran and its children
                $freqTran = Proxy_FreqTran::getInstance()->findById($tran->id_freq_tran);
                $freqTran->delete();//it removes younger transactions
                $children = $freqTran->getChildren();//it can retrieve transactions from another account
                $updatedAccounts = array();
                foreach($children as $childTran){
                    $updatedAccounts[$childTran->id_account] = $childTran->id_account;
                    if($childTran->id_account == $account->id){
                        if($childTran->date > $tran->date) {
                            $deletedTransactions[] = $childTran->id;
                            $childTran->delete();
                        }
                        else {
                            $transactions[] = $childTran;
                        }
                    }
                }
                
                //update accounts
                foreach($updatedAccounts as $idAccount){
                    $yaccount = Proxy_Account::getInstance()->findById($idAccount);
                    $yaccount->benefit = $yaccount->calculateBenefit();
                    $yaccount->save();
                }
                //1. delete youngers
//                $youngerChildren = $this->retrieveYoungerByFreqTranIdAndTransaction($tran->id_freq_tran, $tran);
//                $updatedAccounts = array();
//                foreach($youngerChildren as $ytran){
//                    $updatedAccounts[$ytran->id_account] = $ytran->id_account;
//                    if($ytran->id_account == $account->id) $deletedTransactions[] = $ytran->id;
//                    $ytran->delete();
//                }
                
            }
        } elseif($tran->is_frequent){//anotherwise the user changed value or frequency prps
            $freqTran = Proxy_FreqTran::getInstance()->findById($tran->id_freq_tran);
            
            $freqTran->id_category_type = $tran->id_category_type;
            $freqTran->name = $tran->name;
            $freqTran->frequency_days = $tran->frequency_days;
            $freqTran->frequency_time = $tran->frequency_time;
            $freqTran->value = $tran->value;
            $freqTran->date= $tran->date;
            $freqTran->save();
            
            //if user changed frequency_time or frequency_days
            if($freqTime != $tran->frequency_time || $freqDays != $tran->frequency_days){
                //create copies + delete younger children tran
                //1. delete youngers
                $youngerChildren = $this->retrieveYoungerByFreqTranIdAndTransaction($tran->id_freq_tran, $tran);
                $updatedAccounts = array();
                foreach($youngerChildren as $ytran){
                    $updatedAccounts[$ytran->id_account] = $ytran->id_account;
                    if($ytran->id_account == $account->id) $deletedTransactions[] = $ytran->id;
                    $ytran->delete();
                }
                
                //2. create copies
                //2.1 create copies of current account
                $transactions = array_merge($this->createCopies($freqTran, $account, $tran->date), $transactions);
                //2.2 younger accounts
                $accounts = Proxy_Account::getInstance()->retrieveNoIndependentByUserIdAndMajorThanDate(Contabilidad_Auth::getInstance()->getUser()->id, $tran->date);
                foreach($accounts as $acc){
                    if($acc->id != $account->id){
                        $this->createFrequencyTransactions($acc, $freqTran);
                        $acc->benefit = $acc->calculateBenefit();
                        $acc->save();
                    }
                }
                
            } elseif($name != $tran->name || $value != $tran->value || $idCategoryType != $tran->id_category_type){//update younger transactions
                $youngerChildren = $this->retrieveYoungerByFreqTranIdAndTransaction($tran->id_freq_tran, $tran);
                $updatedAccounts = array();
                foreach($youngerChildren as $ytran){
                    $ytran->name = $tran->name;
                    $ytran->value = $tran->value;
                    $ytran->id_category_type = $tran->id_category_type;
                    $ytran->save();
                    if($ytran->id_account == $account->id){
                        $transactions[] = $ytran;
                    }
                    $updatedAccounts[$ytran->id_account] = $ytran->id_account;
                }
                if($name != $tran->name || $value != $tran->value){
                    //update accounts
                    foreach($updatedAccounts as $idAccount){//@todO REVISAR
                        $yaccount = Proxy_Account::getInstance()->findById($ytran->id_account);
                        $yaccount->benefit = $yaccount->calculateBenefit();
                        $yaccount->save();
                    }
                }
            }
        }
        $account->benefit = $account->calculateBenefit();
        $account->save();
        return array($transactions, $deletedTransactions);
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
    
    public function createCopies($tran, $account, $omiteDate = null){
        $transactions = array();
        $day = 60*60*24;
        $date = $tran->date;
        $max = 1388448000;// => 31/12/2013
        if($tran->frequency_time > 0 && $tran->frequency_days>0){
            $max = $tran->frequency_time*$tran->frequency_days*$day + $tran->date;
        }elseif($tran->frequency_days == -1 && $tran->frequency_time > 0){
            $max = strtotime($tran->date, "+" . $tran->frequency_time . " month");
        }
        if($max > $account->date_ini && $date < $account->date_ini){//if date < date_ini and max is bigger than date_ini
            if($tran->frequency_days>0){
                $diff = $tran->date - $account->date_ini;
                $date = $tran->date + $diff + $tran->frequency_days*$day;
            } elseif($tran->frequency_days == -1) {//monthly
                $tranDay = date("d", $tran->date);
                $accDay = date("d", $account->date_ini);
                $accMonth = date("m", $account->date_ini);
                $accYear = date("Y", $account->date_ini);
                $date = strtotime($tranDay . "-" . $accMonth . "-" . $accYear);
            }
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
                $row->id_quantup = $account->id_quantup;
                $row->value = $tran->value;
                $row->id_category_type = $tran->id_category_type;
                $row->id_transaction_type = $tran->id_transaction_type;
                $row->date = $date;
                $row->id_freq_tran = $tran->id;
                $row->save();

                $transactions[] = $row;
            }
            if($tran->frequency_days == -1){
                $dateDay = date("d", $date);
                $dateMonth = date("m", $date);
                $dateYear = date("Y", $date);
                $dateMonth++;
                if($dateMonth > 12){
                    $dateMonth = 1;
                    $dateYear++;
                }
                $date = strtotime($dateDay . "-" . $dateMonth . "-" . $dateYear);
            } else {
                $date = $date + $tran->frequency_days*$day;
            }
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
        if($tran->date <= $account->date_end){
            $day = 60*60*24;
            $times = 0;
            if($tran->frequency_days > 0){
                $times = ceil(($account->date_ini - $tran->date)/($tran->frequency_days*$day));
                $newDate = $tran->date + ($tran->frequency_days*$day*$times);
            } elseif($tran->frequency_days == -1){
                $accD = date("d", $account->date_ini);
                $accM = date("m", $account->date_ini);
                $accY = date("Y", $account->date_ini);
                $tranD = date("d", $tran->date);

                $accDate = new DateTime($tranD . "-" . $accM . "-" . $accY);
                $tranDate = new DateTime(date("d-m-Y", $tran->date));
                $interval = $accDate->diff($tranDate);
                $times = $interval->y*12 + $interval->m;
                $newDate = strtotime($tranD . "-" . $accM . "-" . $accY);
            }         
            if(($tran->frequency_time == 0 || $times <= $tran->frequency_time)){
                if($newDate <= $account->date_end){//if new date is between period, create tran
                    $tran->date = $newDate;
                    $transactions = Proxy_Transaction::getInstance()->createCopies($tran, $account);
                }
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
        $ar = array('id' => $account->id, 'date_ini' => $account->date_ini, 'date_end' => $account->date_end);
        return $this->retrieveBetweenDatesAndAccountId($ar);
    }
    
    public function retrieveBetweenDatesAndAccountId($params, $order = "date DESC"){
        $id = $params['id'];
        $dateIni = $params['date_ini'];
        $dateEnd = $params['date_end'];
        $select = $this->getTable()->select()
                       ->where("id_account = '$id'")
                       ->where("date >= '$dateIni'")
                       ->where("date <= '$dateEnd'")
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