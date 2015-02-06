<?php

include('lib.php');
$api="https://api.weibo.com/oauth2/authorize";
$canshu=array(
   'redirect_uri'=>$sina_cfg['callback'],
   'client_id'=>$sina_cfg['appkey']
);
header('Location:'.$api.'?'.sina_param($canshu));
?>