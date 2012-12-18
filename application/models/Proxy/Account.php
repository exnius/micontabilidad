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
        $row->id_currency = '1';
        $row->save();
    }

    public function findById ($accountId){
        return $this->getTable()->fetchRow("id = '$accountId'");
    }

    public function retrieveByUserId($userId){
        return $this->getTable()->fetchAll("id_user = '$userId'");
    }
}
?>
