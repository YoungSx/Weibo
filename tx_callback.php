<?php
if(!$_GET) exit;
if($_GET['code']){
   include('lib.php');
   $url='https://open.t.qq.com/cgi-bin/oauth2/access_token';
   $code=$_GET['code'];
   $grant_type='authorization_code';
   $state='1223';
   $canshu=array(
      'grant_type'=>$grant_type,
      'client_id'=>$tx_cfg['appid'],
      'client_secret'=>$tx_cfg['appkey'],
      'code'=>$code,
      'state'=>$state,
      'redirect_uri'=>$tx_cfg['callback']
   );
   foreach($canshu as $key=>$val){
      $a[].=$key.'='.urlencode($val);
   }
   $cans=join('&',$a);

$url = ($url.'?'.$cans); 
$result = request($url);
$result=explode('&',$result);

foreach($result as $val){
   $r=explode('=',$val);
   $k[].=$r[0];
   $v[].=$r[1];
}
$info=array_combine($k,$v);
}

// '过期时间：'.$info['expires_in'];
////////////////////////////////////////刷新access_token
$info=array_combine($k,$v);
$access_token=$info['access_token'];
$refresh_token=$info['refresh_token'];

$_SESSION['tx']['openid']=$info['openid'];
$_SESSION['tx']['access_token']=$access_token;
$_SESSION['tx']['refresh_token']=$refresh_token;
$_SESSION['tx']['name']=$info['name'];
$_SESSION['tx']['nick']=$info['nick'];


setcookie('tx',$access_token.'|'.$refresh_token.'|'.$openid,time()+60*60*24*7);

if($access_token)header('Location: home.php');
//refresh_token();
/*
access_token=23b0caa99fb42442ed5d9e30c61823c9
&expires_in=604800
&refresh_token=66a1075ccb01582de5672deeee1d0fca
&openid=7243222239062e94c7b4c8be467edf65
&name=shangxin9515
&nick=杨尚鑫
&state=12231
*/
?>