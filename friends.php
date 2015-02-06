<?php
include('lib.php');
if(!isset($_SESSION['tx']['access_token']) || !isset($_SESSION['sina']['access_token'])) exit;
$sina=new sina($_SESSION['sina']['access_token']);
$tx=new tx($_SESSION['tx']['access_token'],$tx_cfg['appid']);
if(!isset($_GET['type'])) exit;
if(isset($_GET['page'])) $page=$_GET['page'];else $page=0;
$type=$_GET['type'];
switch($_GET['type']){
case 0://腾讯粉丝
   $result=$tx->fanslist($page,$type);
   $result=json_decode($result,true);
   $result=$result['data'];
   $result=$result['info'];
   $host='http://t.qq.com/';
   $profile_url='name';
   $name='nick';
   $title='<div class="friendsTitle">腾讯 - 听众</div>';
   break;
case 1://腾讯偶像
   $result=$tx->fanslist($page,$type);
   $result=json_decode($result,true);
   $result=$result['data'];
   $result=$result['info'];
   $host='http://t.qq.com/';
   $profile_url='name';
   $name='nick';
   $title='<div class="friendsTitle">腾讯 - 收听</div>';
   break;
case 2://新浪粉丝
   $result=$sina->friends('fans');
   $result=$result['users'];
   $host='http://www.weibo.com/';
   $profile_url='profile_url';
   $name='name';
   $title='<div class="friendsTitle">新浪 - 粉丝</div>';
   break;
case 3://新浪偶像
   $result=$sina->friends('idol');
   $result=$result['users'];
   $host='http://www.weibo.com/';
   $profile_url='profile_url';
   $name='name';
   $title='<div class="friendsTitle">新浪 - 关注</div>';
   break;
default:
   exit;
}

$list='<ul>';
if($result){
   foreach($result as $val){
      $list.='<li><a href='.$host.$val[$profile_url].'>'.$val[$name].'</a></li>';
   }
}
$list.='</ul>';
$list=$title . $list;
echo $list;
/*
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
*/
?>