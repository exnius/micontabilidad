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
    
    public function createNew($user, $quantup, $params){
        $row = $this->createRow();
        $row->id_user = $user->id;
        $row->id_quantup = $quantup->id;
        $row->name = $params['name'];
        $row->date_ini = $params['date_ini'];
        $row->date_end = $params['date_end'];
        $row->benefit = '0';
        $row->id_view_type = '1';
        $row->is_independent = isset($params['is_independent']) ? $params['is_independent'] : false;
        $row->id_currency = $params['id_currency'];
        $row->creation_date = time();
        $row->picture_url = isset($params['picture_url']) ? $params['picture_url'] : null;
        $row->details = $params['details'];
        $row->save();
        
        if(!$row->is_independent){
            Proxy_Transaction::getInstance()->createAllFrequencyTransactions($row);
        }
        
        $row->benefit = $row->calculateBenefit();
        $row->save();
        return $row;
    }

    public function editAccount ($account, $params){
       $account->name = $params['name'];
       $account->date_ini = $params['date_ini'];
       $account->date_end = $params['date_end'];
       $account->id_currency = $params['id_currency'];
       $account->is_independent = $params['is_independent'];
       $benefit = $account->calculateBenefit();
       $account->benefit = $benefit;
       $account->picture_url = $params['picture_url'];
       $account->details = $params['details'];
       $account->save();
       return $account;
    }
    
    public function retrieveNoIndependentByUserIdAndMajorThanDate($userId, $date){
        $select = $this->getTable()->select()
                       ->where("id_user = '$userId'")
                       ->where("is_independent = '0'")
                       ->where("date_ini >= '$date'");
        return $this->getTable()->fetchAll($select);
    }

    public function findById ($accountId){
        return $this->getTable()->fetchRow("id = '$accountId'");
    }

    public function retrieveByUserId($userId , $order="id DESC"){
        return $this->getTable()->fetchAll("id_user = '$userId'", $order);
    }
    
    public function retrieveByQuery ($query){
        return $this->getTable()->fetchAll($query);
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
                                   'is_independent' => $account->is_independent, 
                                   'picture_url' => $account->getPictureUrl(), 
                                   'details' => $account->details,
                                   'accountUrl' => Proxy_Account::getUrl_($account));
    }
}
?>
