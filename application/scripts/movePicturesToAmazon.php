<?php
$query = "WHERE picture_url LIKE %base.url%";
$accounts = Proxy_Account::getInstance()->retrieveByQuery($query);
$users = Proxy_User::getInstance()->retrieveByQuery($query);
$bucketName = "quantups";
$s3 = new S3(awsAccessKey, awsSecretKey);
$s3->putBucket($bucketName, S3::ACL_PUBLIC_READ);

if ($accounts)
{
    foreach ($accounts as $account){
    $accPictureUrl = $account->picture_url;
    $name = explode('/', $account->picture_url);
    $accPictureName = $name[(sizeof($name))-1];
    $ext = explode('.', $account->picture_url);
    $pictureExt = $ext[(sizeof($ext))-1];
    $newAccPictureName = $account->id_user . $account->id . "account_picture" . $pictureExt;
    $s3->putObjectFile($newAccPictureName, $bucketName, $accPictureUrl, S3::ACL_PUBLIC_READ);
    }
} 

if ($users){
    foreach ($users as $user){
        $userPictureUrl = $user->picture_url;
        $name = (explode('/', $user->picture_url));
        $userPictureName = $name[(sizeof($name))-1];
        $ext = explode('.', $user->picture_url);
        $pictureExt = $ext[(sizeof($ext))-1];
        $newUserPictureName = $user->id . "user_picture" . $pictureExt;
        $s3->putObjectFile($newUserPictureName, $bucketName, $accPictureUrl, S3::ACL_PUBLIC_READ);
    }
}
?>
