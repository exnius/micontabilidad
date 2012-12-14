<?php
class Proxy_User extends Contabilidad_Proxy
{
    
    protected static $_instance = null;

    public static function getInstance ()
    {
        if (null === self::$_instance) {
            self::$_instance = new self('user', 'VO_User');
        }
        return (self::$_instance);
    }
    
    public function createNew($params){
        $row = $this->createRow();
        $row->full_name = $params['full_name'];
        $row->password = $params['password'];
       // $row->email = $params['email'];
        $row->email = $this->checkEmail($row->email);
        $row->id_currency = 1;
        $row->nickname = Contabilidad_Utils_String::cleanString($row->full_name);
        $row->save();
        $row->nickname = $this->createNickname($row->full_name);
        
        function checkEmail($email){
            $mail_correcto = 0;
            //compruebo unas cosas primeras
            if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){
               if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) {
                  //miro si tiene caracter .
                  if (substr_count($email,".")>= 1){
                     //obtengo la terminacion del dominio
                     $term_dom = substr(strrchr ($email, '.'),1);
                     //compruebo que la terminación del dominio sea correcta
                     if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){
                        //compruebo que lo de antes del dominio sea correcto
                        $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1);
                        $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1);
                        if ($caracter_ult != "@" && $caracter_ult != "."){
                           $mail_correcto = 1;
                        }
                     }
                  }
               }
            }
            if ($mail_correcto)
               return 1;
            else
               return 0;
        } 
        
            }
    
    public function findByEmail($email){
        return $this->getTable()->fetchRow("email = '$email'");
    }
    
    private function findByNickname ($nickname){
        
    }

    
}