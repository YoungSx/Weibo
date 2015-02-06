<?php
include('lib.php');
if(!isset($_SESSION['tx']['access_token'])) exit;
if(!isset($_SESSION['sina']['access_token'])) exit;
$tx=new tx($_SESSION['tx']['access_token'],$tx_cfg['appid']);
$sina=new sina($_SESSION['sina']['access_token']);
$sina->timeline=$sina->timeline();
$tx->timeline=$tx->timeline();
//request('http://127.0.0.1/t/update.php?tx_type=5&tx_op=1');
$tx->update(5,1);
$timeline=to_list(order(array_merge($sina->timeline,$tx->timeline)));
echo $timeline;
?>