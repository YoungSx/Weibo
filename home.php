<?php
require('lib.php');
echo <<<EOT
<!DOCTYPE html>
<html>
 <head>
  <link href="Style/style2.css" rel='stylesheet' type='text/css'/>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=no"/>
  <title>Ysx版微博 - 我每天都在进步！</title>
  <script type="text/javascript" src="Js/script.js"></script>
  <script type="text/javascript">
  </script>
 </head>
 <body onload="yc('tips');getTimeline();">
  <div id="main">
  <div id='title'><a href=home.php>Ysx版微博</a></div>
EOT;

if(!isset($_SESSION['tx']['access_token'])){
   if(isset($_COOKIE['tx'])){
	  $cookie=$_COOKIE['tx'];
	  $cookie=explode("|",$cookie);
	  $access_token=iconv('ASCII','UTF-8',$cookie[0]);
	  $refresh_token=iconv('ASCII','UTF-8',$cookie[1]);
	  $openid=iconv('ASCII','UTF-8',$cookie[2]);
	  if(!$access_token || !$refresh_token || !$openid){
	     unset($_COOKIE['tx']);
		 toauth('tx');
	  }
	  $tx=new tx($access_token,$tx_cfg['appid']);
	  $tx->refresh_token($refresh_token);
	  $tx->access_token=$_SESSION['tx']['access_token'];
	  $access_token=$_SESSION['tx']['access_token'];
	  $refresh_token=$_SESSION['tx']['refresh_token'];
	  $openid=$_SESSION['tx']['openid'];
	  setcookie('tx',$access_token.'|'.$refresh_token.'|'.$openid,time()+60*60*24*7);
   }else{
      toauth('tx');
   }
   
   //toauth('tx');
}else{
   $tx=new tx($_SESSION['tx']['access_token'],$tx_cfg['appid']);
   
}

if(!isset($_SESSION['sina']['access_token'])){
   if(isset($_COOKIE['sina'])){
      $cookie=iconv('ASCII','UTF-8',$_COOKIE['sina']);
	  $_SESSION['sina']['access_token']=$cookie;
	  $sina=new sina($_SESSION['sina']['access_token']);
   }else toauth('sina');
}else $sina=new sina($_SESSION['sina']['access_token']);

function toauth($plat){
   global $tx_cfg,$sina_cfg;
   $api=array(
      'tx'=>'https://open.t.qq.com/cgi-bin/oauth2/authorize',
	  'sina'=>'https://api.weibo.com/oauth2/authorize'
   );
   if($plat=='tx'){
      $cs = array('response_type'=>'code','client_id'=> $tx_cfg['appid'] , 'redirect_uri' => $tx_cfg['callback'] );
      foreach( $cs as $key => $val){
         $a[]=$key.'='.urlencode($val);
      }
      $cans.=join('&',$a);
	  header('Location: '.$api[$plat].'?'.$cans);
   }else{
      $canshu=array(
         'redirect_uri'=>$sina_cfg['callback'],
         'client_id'=>$sina_cfg['appkey']
      );
      header('Location:'.$api[$plat].'?'.sina_param($canshu));
   }
}
$tx->access_token=$_SESSION['tx']['access_token'];
$tips='';
/////////////////////////////////发微博
if(isset($_POST['tw'])){
   $content=$_POST['tw'];
   if($_POST['longitude']&&$_POST['latitude']){
      $longitude=$_POST['longitude'];
      $latitude=$_POST['latitude'];
   }else{
      $longitude=0;
      $latitude=0;
   }
   $sina_result=json_decode($sina->tweet($content,$longitude,$latitude),true);
   $tx_result=json_decode($tx->tweet($content,$longitude,$latitude),true);
   if($tx_result['errcode']==0 && !isset($sina_result['error']))$tips= '发表成功！';
}
///////////////////////////////// 转播、评论
if(isset($_POST['zpcont']) && isset($_POST['id'])){
   if($_POST['plat']=='tx'){
      if($tx->retweet($_POST['zpcont'],$_POST['id'],$_POST['cz']))$tips='操作成功!';
      else $tips='操作失败!';
   }else{
      if(!$_POST['xid'])$result=$sina->retweet($_POST['zpcont'],$_POST['id'],$_POST['cz']);
      else $result=$sina->reply($_POST['zpcont'],$_POST['id'],$_POST['cz'],$_POST['xid']);
	  if($result)$tips='操作成功!';
	  else $tips='操作失败!';
   }

}
///////////////////////////////// +1
if(isset($_GET['like']) && $_GET['like']){
   if($_GET['plat']=='tx'){
      if($tx->retweet('+1',$_GET['like'],0))$tips='操作成功！';
      else $tips='操作失败！';
   }else{
      //if($sina->retweet('+1',$_GET['like'],0))$tips='操作成功';
      //else $tips='操作失败';
	  if($sina->retweet('+1',$_GET['like'],0))$tips='操作成功！';
	  else $tips='操作失败！';
   }
}
///////////////////////////////// 翻页 //////////////合并！！！！！！！
//if(isset($_GET['page'])) $timeline=$tx->tl_list($_GET['page']);
//else $timeline=$tx->tl_list(0);
/*
$sina->timeline=$sina->timeline();
$tx->timeline=$tx->timeline();
$timeline=to_list(order(array_merge($sina->timeline,$tx->timeline)));
*/
/////////////////////////////////
if($tips!='')$tips="<div id='tips'>".$tips.'</div>';
$tx_pro=$tx->info();
if(!$tx_pro) toauth('tx');
$tx_profile=array(
   'fansnum'=>$tx_pro['fansnum'],
   'idolnum'=>$tx_pro['idolnum'],
   'nick'=>$_SESSION['tx']['nick'],
   'name'=>$_SESSION['tx']['name'],
   'new_fans_num'=>'',
   'new_at_num'=>''
);
$sina_pro=$sina->info();
if(!$sina_pro['name']) toauth('sina');
$sina_profile=array(
   'fansnum'=>$sina_pro['followers_count'],
   'idolnum'=>$sina_pro['friends_count'],
   'name'=>$sina_pro['name'],
   'new_fans_num'=>'',
   'new_at_num'=>'',
   'cmt'=>'',
   'mention_cmt'=>''
);
echo <<<AAA
  $tips
  <div id='head'>
   <div id="aprofile" onclick="apro_onclick(this);">
   <div class='profile' id='tx_profile'>
    腾讯：
    <a class="head_name">$tx_profile[nick](@$tx_profile[name])</a><br />
    <span onclick="friends(0)">听众:$tx_profile[fansnum]<span id="tx_new_fans_num">$tx_profile[new_fans_num]</span></span> <span onclick="friends(1)">收听:$tx_profile[idolnum]</span><br />
	<span onclick="messageBox(4)">@提到我的<span id="tx_new_at_num">$tx_profile[new_at_num]</span></span>
   </div>
   <div class='profile' id='sina_profile'>
    新浪：
    <span class="head_name">@$sina_profile[name]</span><br />
    <span onclick="friends(2)">粉丝:$sina_profile[fansnum]<span id="sina_new_fans_num">$sina_profile[new_fans_num]</span></span> 
	<span onclick="friends(3)">关注:$sina_profile[idolnum]</span><br />
	<span onclick="messageBox(1)">@提到我的<span id="sina_new_at_num">$sina_profile[new_at_num]</span></span>
	<span onclick="messageBox(2)">评论<span id="sina_cmt">$sina_profile[cmt]</span></span> 
   </div>
   </div>
	<form action='home.php' method='post'>
    <textarea name='tw' required="required" placeholder="说点啥？"></textarea><div class="bar">
    <input type="button" onclick="navigator.geolocation.getCurrentPosition(info,cuowu);" id='local' value="未定位"/>
    <input type="hidden" value="" id="latitude" name="latitude"/><input value="" type="hidden" id="longitude" name="longitude"/>
    <input type="submit" value="发微博！"/></div>
   </form>
  </div>
  <!--<div onclick="openAtBox(this)">打开</div>-->
  <div class="timeline" id="message">加载中...</div>
  <div class="" onclick="getTimeline();" id="refresh">刷新</div>
  <div class='timeline' id="maintimeline"></div>
  <div class=foot><a href=home.php?page=-1>&lt;</a> 翻页 <a href=home.php?page=1>&gt;</a></div>
  <div class="switch" onclick="Sswitch(this);">重力滚屏</div>
  <div id="topcontrol" onclick="window.location.href='#'">顶部</div>
  <div id="back" onclick="back(this)">返回</div>
  </div>
  </body>
</html>
AAA;

?>