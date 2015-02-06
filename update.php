<?php
include('lib.php');
if(!isset($_SESSION['tx']['access_token'])) exit;
if(!isset($_SESSION['sina']['access_token'])) exit;
$tx=new tx($_SESSION['tx']['access_token'],$tx_cfg['appid']);
$sina=new sina($_SESSION['sina']['access_token']);
//if(isset($_GET['sina_type'])) $sina_type=$_GET['sina_type'] else $sina_type='';
//if(isset($_GET['sina_op'])) $sina_type=$_GET['sina_op'] else $sina_op='';

$tx_update=$tx->update();
if($tx_update){
   $tx_update=$tx_update['data'];
   if($tx_update['fans']!=0)$tx_profile['new_fans_num']='['.$tx_update['fans'].']';else $tx_profile['new_fans_num']='';
   if($tx_update['mentions']!=0)$tx_profile['new_at_num']='['.$tx_update['mentions'].']';else $tx_profile['new_at_num']='';
}else{
   echo '出错';
   exit;
}
$sina_update=$sina->update();
if($sina_update){
   if($sina_update['follower']!=0)$sina_profile['new_fans_num']='['.$sina_update['follower'].']';else $sina_profile['new_fans_num']='';
   if($sina_update['mention_status']!=0)$sina_profile['new_at_num']='['.$sina_update['mention_status'].']';else $sina_profile['new_at_num']='';
   if($sina_update['cmt']!=0)$sina_profile['cmt']='['.$sina_update['cmt'].']';else $sina_profile['cmt']='';
   if($sina_update['mention_cmt']!=0)$sina_profile['mention_cmt']='['.$sina_update['mention_cmt'].']';else $sina_profile['mention_cmt']='';
}else{
   echo '出错';
   exit;
}
//echo $sina_update['status'];//$tx_update['home'];
$status = $tx_update['home']+$sina_update['status'];
$update=array(
   'tx'=>$tx_profile,
   'sina'=>$sina_profile,
   'status'=>$status
);
$content=json_encode($update);
echo $content;
?>