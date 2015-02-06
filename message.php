<?php
include('lib.php');
if(!isset($_SESSION['tx']['access_token']) || !isset($_SESSION['sina']['access_token'])) exit;
//if(!isset($_SESSION['sina']['access_token'])) exit;
if(!isset($_GET['type'])) exit;
if(isset($_GET['page'])) $page=$_GET['page'];
else $page=0;
$sina=new sina($_SESSION['sina']['access_token']);
switch($_GET['type']){
case 1:
   $result = $sina->mentions();
   $sina->set_count('mention_status');
   $result=json_decode($result,1);
   $info=$sina->format($result);
   break;
case 2:
   $result = $sina->to_me();
   $sina->set_count('cmt');
   $result=json_decode($result,1);
   $info=$sina->format($result,'comments','status');
   break;
case 3:
   $result = $sina->comments_mentions();
   $sina->set_count('mention_cmt');
   $result=json_decode($result,1);
   $info=$sina->format($result,'comments','status');
   break;
case 4:
   $tx=new tx($_SESSION['tx']['access_token'],$tx_cfg['appid']);
   $tx->update(6,1);
   $info = $tx->at($page);
   break;
default:
   exit;
}

echo to_list($info);
?>