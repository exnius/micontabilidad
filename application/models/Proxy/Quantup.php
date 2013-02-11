<?php
class Proxy_Quantup extends Contabilidad_Proxy
{
    protected static $_instance = null;

    /**
     * @return Public Proxy_Primitives
     */
    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('quantup', 'VO_Quantup');
        }
        return (self::$_instance);
    }
    
    public function createNew($user, $params){
        $row = $this->createRow();
        $row->id_user = $user->id;
        $row->name = $params['name'];
        $row->is_predetermined = isset($params['is_predetermined']) ? $params['is_predetermined'] : false;
        $row->creation_date = time();
        $row->picture_url = isset($params['picture_url']) ? $params['picture_url'] : null;
        $row->save();
        return $row;
    }
    
    public function findPredeterminedByUserId($userId){
        $select = $this->getTable()->select()
                       ->where("id_user = '$userId'")
                       ->where("is_predetermined = '1'");
        return $this->getTable()->fetchRow($select);
    }
    
    public function findById ($quantupId){
        return $this->getTable()->fetchRow("id = '$quantupId'");
    }
    
    
    /*
     * Create URL from VO_Quantup
     * 
     * @return string
     * @params VO_Quantup
     */
    public static function getUrl_ ($quantup){
        $url = BASE_URL;
        return $url;
    }
    
    public function serializer ($quantup){
        return $serialized = array('id' => $quantup->id, 
                                   'name' => $quantup->name, 
                                   'quantupUrl' => Proxy_Account::getUrl_($quantup));
    }
}
?>
