<?
include('lib.php');
if(!isset($_SESSION['tx']['access_token'])) exit;
$tx=new tx($_SESSION['tx']['access_token'],$tx_cfg['appid']);
$file = $_FILES['file']['tmp_name'];//['tmp_name']
$tx->pic_tweet('test!',$file);
?>