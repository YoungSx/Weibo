<?php
include('lib.php');
if(!isset($_SESSION['tx']['access_token'])) exit;
$tx=new tx($_SESSION['tx']['access_token'],$tx_cfg['appid']);

$tx->update(6,1);//清空未读数
if(isset($_GET['page'])) $at=$tx->at($_GET['page']);
else $at=$tx->at(0);
echo to_list($at);
?>