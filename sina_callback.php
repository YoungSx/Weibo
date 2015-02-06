<?php
include('lib.php');
$api="https://api.weibo.com/oauth2/access_token";
$http_method="POST";
$canshu=array(
   'redirect_uri'=>$sina_cfg['callback'],
   'client_id'=>$sina_cfg['appkey'],
   'client_secret'=>$sina_cfg['appsecret'],
   'grant_type'=>'authorization_code',
   'code'=>$_GET['code']
);

$result= request($api.'?'.sina_param($canshu),'POST');
$result=json_decode($result,true);
$_SESSION['sina']['access_token']=$result['access_token'];
setcookie('sina',$_SESSION['sina']['access_token'],time()+60*60*24*7);
if($_SESSION['sina']['access_token'])header('Location: home.php');
?>