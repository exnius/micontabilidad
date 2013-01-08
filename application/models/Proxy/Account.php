<?php
class Proxy_Account extends Contabilidad_Proxy
{
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('account', 'VO_Account');
        }
        return (self::$_instance);
    }
    
    public function createNew($user, $params){
        $row = $this->createRow();
        $row->id_user = $user->id;
        $row->name = $params['name'];
        $row->date_ini = $params['date_ini'];
        $row->date_end = $params['date_end'];
        $row->benefit = '0';
        $row->id_view_type = '1';
        $row->id_currency = $params['id_currency'];
        $row->creation_date = time();
        $row->save();
        
        $transactions = Proxy_FreqTran::getInstance()->retrieveAllByUserId($user->id);
        $day = 60*60*24;
        foreach($transactions as $tran){
            //if frequency time is infinite, it will be a tran of current account
            if($tran->frequency_time == 0 && $tran->date <= $row->date_ini){
                $diff = $row->date_ini - $tran->date;
                $newDate = $tran->date + $diff + $tran->frequency_days*$day;
                if($newDate <= $row->date_end){//if new date is between period, create tran
                    $acctra = Proxy_Transaction::getInstance()->createCopies($tran, $account);
                }
            }
        }
        $row->calculateBenefit();
        $row->save();
        return $row;
    }

    public function editAccount ($params){
       $account = $this->findById($params['id']);
       $account->name = $params['name'];
       $account->date_ini = $params['date_ini'];
       $account->date_end = $params['date_end'];
       $account->id_currency = $params['id_currency'];
       $benefit = $account->calculateBenefit();
       $account->benefit = $benefit;
       $account->save();
       return $account;
    }

    public function findById ($accountId){
        return $this->getTable()->fetchRow("id = '$accountId'");
    }

    public function retrieveByUserId($userId , $order="id DESC"){
        return $this->getTable()->fetchAll("id_user = '$userId'", $order);
    }
    
    
    /*
     * Create URL from VO_Account
     * 
     * @return string
     * @params VO_Account
     */
    public static function getUrl_ ($account){
        $url = BASE_URL . "/private/account/index?id=" . $account->id;
        return $url;
    }
    
    public function serializer ($account){
        return $serialized = array('id' => $account->id, 
                                   'name' => $account->name, 
                                   'benefit' => $account->benefit, 
                                   'date_ini' => $account->date_ini, 
                                   'date_end' => $account->date_end, 
                                   'id_currency' => $account->id_currency, 
                                   'accountUrl' => Proxy_Account::getUrl_($account));
    }
}
?>
