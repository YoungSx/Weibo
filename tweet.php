<?php
include('lib.php');
if(!isset($_SESSION['tx']['access_token'])) exit;
if(!isset($_SESSION['sina']['access_token'])) exit;
$tx=new tx($_SESSION['tx']['access_token'],$tx_cfg['appid']);
$sina=new sina($_SESSION['sina']['access_token']);

$tx=new tx($_SESSION['tx']['access_token'],$tx_cfg['appid']);
if(!isset($_POST['tw'])){ 
echo <<<EOT
<html>
 <body>
  <form action='tweet.php' method='post'>
   <textarea name='tw'></textarea><br/>
   <input type='submit' value='发一条微博！'/>
  </form>
 </body>
</html>
EOT;
exit;
}
$access_token=$_SESSION['tx']['access_token'];
$content=$_POST['tw'];
$result=$tx->tweet($content);
$result=json_decode($result,true);
if($result['errcode']==0)echo '发表成功！<a href=index.php>返回</a>';
else echo print_r($result);

?>