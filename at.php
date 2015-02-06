<?php
include('lib.php');
if(!isset($_SESSION['tx']['access_token'])) exit;
$tx=new tx($_SESSION['tx']['access_token'],$tx_cfg['appid']);
echo "
<html>
 <head>
  <link href=\"Style/style.css\" rel='stylesheet' type='text/css'/>
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes\">
  <title>Ysx Home</title>
  <script type=\"text/javascript\" src=\"/t/Js/script.js\">
  </script>
 </head>
 <body><div id='title'><a href=home.php>Ysx Home</a></div>
";
$update=$tx->update(6,1);
if($update){
   $update=$update['data'];
   $new_fans_num=$update['mentions'];
}
   echo "<div id=\"new_fans_num\">新[$new_fans_num]</div><br />";
if(isset($_GET['page'])) $at=$tx->at_list($_GET['page']);
else $at=$tx->at_list(0);
echo $at;
echo "<div class=fanye><a href=at.php?page=-1>&lt;</a> 翻页 <a href=at.php?page=1>&gt;</a></div></body></html>";
?>