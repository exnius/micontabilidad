<?php

class Private_UserController extends Zend_Controller_Action
{
    public function findAction(){
        $this->view->pru="find";
    }
    
    public function editAction(){
        $user = Contabilidad_Auth::getInstance()->getUser();
        $this->view->user = $user;
        $currencys = Proxy_Currency::getInstance()->retrieveCurrencys();
        $this->view->currencys = $currencys;
    }
    
    public function uploadpictureAction(){
        $this->view->root = ROOT;
        $ruta= ROOT . "/public/pictures/";//ruta carpeta donde queremos copiar las imágenes
        $uploadfile_temp=$_FILES['fichero']['tmp_name'];
        $userId = Contabilidad_Auth::getInstance()->getUser()->id;
        $picture = $userId . '_picture.jpg';
        $uploadfile_name=$ruta.$_FILES['fichero']['name']= $picture;

        if (is_uploaded_file($uploadfile_temp))
        {
            move_uploaded_file($uploadfile_temp,$uploadfile_name);
            $this->_redirect(BASE_URL . "/private/user/edit");
        }
        else
        {
        echo "error";
        }
        $directorio=opendir("pictures/");
        while($ficheros=readdir($directorio))
        {
            $url="imagenes/".$ficheros;
            echo "<img src=".$url.">";
        } 
     }
}
?>
