<?php
include('lib.php');
if(!isset($_SESSION['tx']['access_token'])) exit;
$tx=new tx($_SESSION['tx']['access_token'],$tx_cfg['appid']);
if(!isset($_GET['type']))exit;
if($_GET['page']) $page=$_GET['page'];else $page=0;
$type=$_GET['type'];
echo "<html>
 <head>
  <link href=\"Style/style.css\" rel='stylesheet' type='text/css'/>
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes\">
  <title>Ysx Home</title>
  <script type=\"text/javascript\" src=\"/t/Js/script.js\">
  </script>
 </head>
 <body><div id='title'><a href=home.php>Ysx Home</a></div>";
if($type==0){
   $updata=$tx->updata(8,1);
   if($updata){
      $updata=$updata['data'];
      $new_fans_num=$updata['fans'];
   }
   echo "<div id=\"new_fans_num\">新听众[$new_fans_num]</div><br />";
}
////////////////////////////////////////
$fans1=$tx->fanslist($page,$type);
$fans2=json_decode($fans1,true);
$fans3=$fans2['data'];
$fans=$fans3['info'];
foreach($fans as $val){
   if($type==0 && $val['isidol']==1)//粉丝&&我收听      0粉丝 1偶像
      $mark='[互听]';
   elseif($type==1 && $val['isfans']==1)$mark='[互听]';
   else $mark='';
   
   echo '<a href=http://t.qq.com/'.$val['name'].'>'.$val['nick'].'</a> '.
   $mark
   .'<br/>';
}
$p1=$page-1;
$p2=$page+1;
if($page>0)echo "
<a href=fans.php?type=$type&page=$p1>上一页</a>";

echo "
<a href=fans.php?type=$type&page=$p2>下一页</a>
";

//////////////////////////////////
?>