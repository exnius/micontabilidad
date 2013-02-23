<?php
    // Script: Update pictures from Quantups to Amazon

set_include_path(".");

$succesfully = false;
//register shutdown functions
//require_once 'shutdown.inc.php';

require_once realpath(dirname(__FILE__)) . '/../../public/includes.php';

// AutoLoader not started yet
require_once 'Contabilidad/Initializer.php';

// init
new Initializer('production');

//include the S3 class
if (!class_exists('S3'))require_once('S3.php');

//AWS access info
$config = Zend_Registry::get('Config');
if (!defined('awsAccessKey')) define('awsAccessKey', $config->api->amazon->aws->clientId);
if (!defined('awsSecretKey')) define('awsSecretKey', $config->api->amazon->aws->secret);


// Initializes objects
$s3 = new S3(awsAccessKey, awsSecretKey);
$query = "picture_url LIKE '%" . BASE_URL . "%'";
$accounts = Proxy_Account::getInstance()->retrieveByQuery($query);
$users = Proxy_User::getInstance()->retrieveByQuery($query);


// Move & resize budgets pictures
if ($accounts->count() != 0){
    $root = ROOT . '/public/quantups_pictures/';
    $bucketName = "quantups_pictures";
    $s3->putBucket($bucketName, S3::ACL_PUBLIC_READ);
    $bucket_contents = $s3->getBucket($bucketName);
    $objectsToDel = read_all_files($root);
    foreach ($accounts as $account){
        $explodeUrl =  explode('/', $account->picture_url);
        $accPictureName = $explodeUrl[(sizeof($explodeUrl))-1];
        
        // Resizing pictures before moving
        $resizeObj = new resizePicture($root . $accPictureName);
        $resizeObj -> resizeImage(200, 200);
        $resizeObj -> saveImage($root . $accPictureName);
        
        // Move pictures to Amazon
        $ext = explode('.', $account->picture_url);
        $name = $account->id_user . "_" . $account->id . "_" . "account_picture";
        $pictureExt = $ext[(sizeof($ext))-1];
        $newAccPictureName =  $name . "." . $pictureExt;
        foreach(array("jpg" , "png", "gif") as $extension){
            $url = "http://$bucketName.s3.amazonaws.com/" . $name . "." . $extension;
            if (!$fp = curl_init($url)) {
                return false;
            } else {
                $s3->deleteObject($bucketName, $name . "." . $extension);
            }
        }
        $s3->putObjectFile($root . $accPictureName, $bucketName, $newAccPictureName, S3::ACL_PUBLIC_READ);
        $account->picture_url = "http://$bucketName.s3.amazonaws.com/" . $newAccPictureName;
        $account->save();
    }
    
    // Delete local objects
    foreach ($objectsToDel['files'] as $file){
        unlink($file);
    }
}

// Move & resize avatars pictures
 if ($users->count() != 0){
    $root = ROOT . "/public/avatars/";
    $bucketName = "quantups_avatars";
    $s3->putBucket($bucketName, S3::ACL_PUBLIC_READ);
    $bucket_contents = $s3->getBucket($bucketName);
    $objectsToDel = read_all_files($root);
    foreach ($users as $user){
        $explodeUrl = (explode('/', $user->picture_url));
        $userPictureName = $explodeUrl[(sizeof($explodeUrl))-1];
        
        // Resizing pictures before moving
        $resizeObj = new resizePicture($root . $userPictureName);
        $resizeObj -> resizeImage(200, 200);
        $resizeObj -> saveImage($root . $userPictureName);
        
        // Move pictures to Amazon
        $ext = explode('.', $user->picture_url);
        $name = $user->id . "_" . "avatar";
        $pictureExt = $ext[(sizeof($ext))-1];
        $newUserPictureName =  $name . "." . $pictureExt;
        foreach(array("jpg" , "png", "gif") as $extension){
            $url = "http://$bucketName.s3.amazonaws.com/" . $name . "." . $extension;
            if (!$fp = curl_init($url)) {
                return false;
            } else {
                $s3->deleteObject($bucketName, $name . "." . $extension);
            }
        }
        $s3->putObjectFile($root . $userPictureName, $bucketName, $newUserPictureName, S3::ACL_PUBLIC_READ);
        $user->picture_url = "http://$bucketName.s3.amazonaws.com/" . $newUserPictureName;
        $user->save();
    }
    
    // Delete local objects
    foreach ($objectsToDel['files'] as $file){
        unlink($file);
    }
}
    
    
    /* Function: return an array
     * array(
     *   'files' => [],
     *   'dirs'  => [],
     * ) 
     */ 
    function read_all_files($root) {
        $files = array('files' => array(), 'dirs' => array());
        $directories = array();
        $last_letter = $root[strlen($root) - 1];
        $root = ($last_letter == '\\' || $last_letter == '/') ? $root : $root . DIRECTORY_SEPARATOR;

        $directories[] = $root;

        while (sizeof($directories)) {
            $dir = array_pop($directories);
            $handle = opendir($dir);
            if ($handle) {
                while (false !== ($file = readdir($handle))) {
                    if ($file == '.' || $file == '..') {
                        continue;
                    }
                    $file = $dir . $file;
                    if (is_dir($file)) {
                        $directory_path = $file . DIRECTORY_SEPARATOR;
                        array_push($directories, $directory_path);
                        $files['dirs'][] = $directory_path;
                    } elseif (is_file($file)) {
                        $files['files'][] = $file;
                    }
                }
                closedir($handle);
            }
        }
        return $files;
    }
?>
