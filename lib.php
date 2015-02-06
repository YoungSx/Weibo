<?php
include('cfg.php');
session_start();
class sina{
   var $access_token,$name;
   var $api=array(
      'timeline'=>'https://api.weibo.com/2/statuses/home_timeline.json',
      'retweet'=>'https://api.weibo.com/2/statuses/repost.json',
      'comments'=>'https://api.weibo.com/2/comments/create.json',
	  'tweet'=>'https://api.weibo.com/2/statuses/update.json',
	  'get_token_info'=>'https://api.weibo.com/oauth2/get_token_info',
	  'show'=>'https://api.weibo.com/2/users/show.json',
	  'unread'=>'https://rm.api.weibo.com/2/remind/unread_count.json',
	  'set_count'=>'https://rm.api.weibo.com/2/remind/set_count.json',
	  'mentions'=>'https://api.weibo.com/2/statuses/mentions.json',
	  'to_me'=>'https://api.weibo.com/2/comments/to_me.json',
	  'comments_mentions'=>'https://api.weibo.com/2/comments/mentions.json',
	  'reply'=>'https://api.weibo.com/2/comments/reply.json',
	  'idol'=>'https://api.weibo.com/2/friendships/friends.json',
	  'fans'=>'https://api.weibo.com/2/friendships/followers.json',
	  'pic'=>'https://upload.api.weibo.com/2/statuses/upload.json'
	  
   );
   function __construct($oauth_access_token){
      $this->access_token=$oauth_access_token;
	  $this->get_token_info();
   }
   function param($new_canshu=array()){//URL参数合成
      $canshu=array(
         'access_token'=>$this->access_token
      );
      if(is_array($new_canshu)) $canshu = array_merge($canshu,$new_canshu);
      foreach($canshu as $key=>$val){
         $a[].=$key.'='.$val;
      }
      $param=join('&',$a);
      return $param;
   }
   function tweet($content,$long=0.0,$lat=0.0){
      $canshu=array(
	     'status'=>$content,
		 'long'=>$long,
		 'lat'=>$lat
	  );
      $url=$this->api['tweet'].'?'.$this->param($canshu);
      return request($url,'POST');
   }
   function retweet($text='',$id,$cz=0){//0转播 1评论
      if($cz==0){
	     $api=$this->api['retweet'];
	     $cont_name='status';
	  }
	  else{
	     $api=$this->api['comments'];
		 $cont_name='comment';
	  }
      $canshu=array(
	     'id'=>$id,
		 $cont_name=>urlencode($text)
	  );
      $url=$api.'?'.$this->param($canshu);
	  $result=request($url,'POST');
	  $result=json_decode($result,true);
      if(!isset($result['error']))return 1;
   }
   function reply($text='',$cid,$id){
      $canshu=array(
	     'cid'=>$cid,
	     'id'=>$id,
		 'comment'=>urlencode($text)
	  );
      $url=$this->api['reply'].'?'.$this->param($canshu);
	  $result=request($url,'POST');
	  $result=json_decode($result,true);
      if(!isset($result['error']))return 1;
   }
   function timeline(){
   $canshu=array(
	  'count'=>10
   );
   $param=$this->param($canshu);
   $url=$this->api['timeline'].'?'.$param;
   $result = request($url);
   $result=json_decode($result,true);
   return $this->format($result);
   }
   function get_token_info(){
      $result = request($this->api['get_token_info'].'?'.$this->param(),'POST');
	  $result=json_decode($result,true);
	  $this->uid=$result['uid'];
   }
   function info(){
      $canshu=array(
         'uid'=>$this->uid
      );
      return json_decode(request($this->api['show'].'?'.$this->param($canshu),'GET'),true);
   }
   function update(){
      $canshu=array(
         'uid'=>$this->uid
      );
      return json_decode(request($this->api['unread'].'?'.$this->param($canshu),'GET'),true);
   }
   function set_count($type){
      $canshu=array(
         'type'=>$type
      );
	  /*
	  follower：新粉丝数、
	  cmt：新评论数、
	  dm：新私信数、
	  mention_status：新提及我的微博数、
	  mention_cmt：新提及我的评论数、
	  group：微群消息数、
	  notice：新通知数、
	  invite：新邀请数、
	  badge：新勋章数、
	  photo：相册消息数、
	  close_friends_feeds：密友feeds未读数、
	  close_friends_mention_status：密友提及我的微博未读数、
	  close_friends_mention_cmt：密友提及我的评论未读数、
	  close_friends_cmt：密友评论未读数、
	  close_friends_attitude：密友表态未读数、
	  close_friends_common_cmt：密友共同评论未读数、
	  close_friends_invite：密友邀请未读数，一次只能操作一项。
	  */
	  $result=request($this->api['set_count'].'?'.$this->param($canshu),'POST');
	  $result=json_decode($result,true);
	  print_r($result);
      //if(!isset($result['error']))return 1;
   }
   function mentions(){
      return request($this->api['mentions'].'?'.$this->param(),'GET');
   }
   function to_me(){
      return request($this->api['to_me'].'?'.$this->param(),'GET');
   }
   function comments_mentions(){
      return request($this->api['comments_mentions'].'?'.$this->param(),'GET');
   }
   function friends($type='fans'){
      $canshu=array(
         'uid'=>$this->uid,
		 'count'=>50,
		 'cursor'=>0,//返回结果的游标，下一页用返回值里的next_cursor，上一页用previous_cursor，默认为0。
      );
	  $result = json_decode(request($this->api[$type].'?'.$this->param($canshu),'GET'),true);
	  if(isset($result['error']))return 0;
	  else return $result;
   }
   
   function format($a,$waiceng='statuses',$neiceng='retweeted_status'){
      foreach($a[$waiceng] as $one){
         $image='';
		 $image_large='';
         $geo='';
         $rt_geo='';
		 if($waiceng=='comments') $comments=1;
		 else $comments=0;
         if(!isset($one['reposts_count']))$one['reposts_count']='';
		 if(!isset($one['comments_count']))$one['comments_count']='';
         if(isset($one['geo']))$geo=$one['geo'];
         if(isset($one[$neiceng])){
            if(isset($one[$neiceng]['thumbnail_pic'])){
			   $image=$one[$neiceng]['thumbnail_pic'];
			   $image_large=$one[$neiceng]['bmiddle_pic'];
			}
            if(isset($one[$neiceng]['geo']))$rt_geo=$one[$neiceng]['geo'];
            $info[]=array(
               'id'=>number_format($one['id'],'','',''),
			   'timestamp'=>strtotime($one['created_at']),
			   'head'=>$one['user']['profile_image_url'],
               'name'=>$one['user']['name'],
               'text'=>$one['text'],
               'count'=>$one['reposts_count'],
               'mcount'=>$one['comments_count'],
	           'geo'=>$geo,
	           'image'=>$image, 
		       'profile_url'=>$one['user']['profile_url'],
		       'uid'=>$one['user']['idstr'],
			   'plat'=>'sina',
			   'comments'=>$comments,
               'type'=>2,
	           'source'=>array(
                  'id'=>number_format($one[$neiceng]['id'],'','',''),
		          'timestamp'=>strtotime($one[$neiceng]['created_at']),
				  'head'=>$one[$neiceng]['user']['profile_image_url'],
                  'name'=>$one[$neiceng]['user']['name'],
                  'text'=>$one[$neiceng]['text'],
                  'count'=>$one[$neiceng]['reposts_count'],
                  'mcount'=>$one[$neiceng]['comments_count'],
		          'profile_url'=>$one[$neiceng]['user']['profile_url'],
		   	      'uid'=>$one[$neiceng]['user']['idstr'],
   		          'geo'=>$rt_geo,
				  'comments'=>$comments,
   		          'image'=>$image,
				  'image_large'=>$image_large
                  )
             );
         }else{//D M d H:i:s Z Y
            if(isset($one['thumbnail_pic'])){
			   $image=$one['thumbnail_pic'];
			   $image_large=$one['bmiddle_pic'];
		    }
            $info[]=array(
               'id'=>number_format($one['id'],'','',''),
			   'timestamp'=>strtotime($one['created_at']),
			   'head'=>$one['user']['profile_image_url'],
               'name'=>$one['user']['name'],
               'text'=>$one['text'],
               'count'=>$one['reposts_count'],
               'mcount'=>$one['comments_count'],
	           'geo'=>$geo,
	           'image'=>$image,
			   'image_large'=>$image_large,
		       'profile_url'=>$one['user']['profile_url'],
		       'uid'=>$one['user']['idstr'],
			   'plat'=>'sina',
			   'comments'=>$comments,
		       'type'=>1
	         );
         }
      }
      return $info;
      }
   function pic_tweet(){
      $canshu=array(
	     'status'=>$content,
         'access_token'=>$this->access_token,
		 
	  );
      $url=$this->api['tweet'].'?'.$this->param($canshu);
      return request($url,'POST');
   }
}


class tx{
   var $access_token,$appid;
function __construct($oauth_access_token,$appid){
   $this->access_token=$oauth_access_token;
   $this->appid=$appid;
}
function fanslist($page=0,$type=0){
   $api1='http://open.t.qq.com/api/friends/fanslist';
   $api2='https://open.t.qq.com/api/friends/idollist_s';
   if($type==0){//0粉丝
      $api=$api1;
   }else{
      $api=$api2;
   }
   
   $cans=array(
      'format'=>'json',//返回格式
	  'reqnum'=>10,//请求个数 1-30
	  'startindex'=>$page,//起始位置（第一页：填0，继续向下翻页：填上次请求返回的nextstartpos）
	  'mode'=>0,//获取模式，默认为0 mode=0，新粉丝在前，只能拉取1000个 mode=1，最多可拉取一万粉丝
	  //'install'=>0,//可选 安装本应用的好友 0忽略 1装 2没装
	  //'sex'=>0,//0忽略 1男 2女
   );
   $param=$this->param($cans);
   $result=request($api.'?'.$param);
   return $result;
}
function retweet($content,$reid,$cz=0){//默认转播
   $api1='https://open.t.qq.com/api/t/re_add';//转播
   $api2='https://open.t.qq.com/api/t/comment';//评论
   if($cz==1)$api=$api2;//如果$cz==1评论
   else $api=$api1;//     否则转播
   $cans=array(
	  'content'=>$content,
      'format'=>'json',
	  'reid'=>$reid
   );
   $param=$this->param($cans);
   $result=request($api.'?'.$param,'POST');
   $result=json_decode($result,1);
   if($result['errcode']==0) return true;
   else return false;
}
function at($pageflag=0,$pagetime=0,$reqnum=10,$type=0,$contenttype=0,$lastid=0){
   switch ($pageflag) {
    case 1://向后
        $pageflag=1;
		$pagetime=$_SESSION['tx']['at_lasttime'];
		$lastid=$_SESSION['tx']['at_lid'];
        break;
    case 2://向前
        $pageflag=2;
		$pagetime=$_SESSION['tx']['at_firsttime'];
		$lastid=$_SESSION['tx']['at_fid'];
        break;
    default:
        $pageflag=0;
		$pagetime=0;
		$lastid=0;
   }
   $api='https://open.t.qq.com/api/statuses/mentions_timeline';
   $cans=array(
      'format'=>'json',
      'pageflag'=>$pageflag,//0：第一页，  向下翻页：1                   向上翻页：2
      'pagetime'=>$pagetime,//第一页：填0  向下翻页：最后一条记录时间    向上翻页：第一条记录时间
      'reqnum'=>$reqnum,//每次条数
	  'type'=>$type,//0x1 原创发表 0x2 转载 如需拉取多个类型请使用|，如(0x1|0x2)得到3，则type=3即可，0所有类型
	  'contenttype'=>$contenttype,//内容过滤。0-表示所有类型，1-带文本，2-带链接，4-带图片，8-带视频，0x10-带音频 建议不使用contenttype为1的类型，如果要拉取只有文本的微博，建议使用0x80
	  'lastid'=>$lastid
   );
   $param=$this->param($cans);
   $result=json_decode(request($api.'?'.$param),1);
   $result=$result['data']['info'];
   
   foreach($result as $one){
      $image='';
	  $image_large='';
      if($one['type']==1){
	     if(isset($one['image'])){
		    $image=$one['image'][0];
			$image_large=$one['image'][0].'/460';
	     }
	     $t[]=array(
		    'id'=>$one['id'],
			'timestamp'=>$one['timestamp'],
			'name'=>$one['nick'],
			'text'=>$one['text'],
			'count'=>$one['count'],
			'mcount'=>$one['mcount'],
			'geo'=>$one['geo'],
			'image'=>$image,
			'image_large'=>$image_large,
			'profile_url'=>$one['name'],
			'uid'=>$one['name'],
			'plat'=>'tx',
			'type'=>$one['type']
		 );
	  }elseif($one['type']==2){
	     if(isset($one['source']['image'])){
		    $image=$one['source']['image'][0];
			$image_large=$one['source']['image'][0].'/460';
		 }
		 $t[]=array(
		 	'id'=>$one['id'],
			'timestamp'=>$one['timestamp'],
			'name'=>$one['nick'],
			'text'=>$one['text'],
			'count'=>$one['count'],
			'mcount'=>$one['mcount'],
			'geo'=>$one['geo'],
			'image'=>$image, 
			'profile_url'=>$one['name'],
			'uid'=>$one['name'],
			'plat'=>'tx',
			'type'=>$one['type'],
			'source'=>array(
		       'id'=>$one['source']['id'],
			   'timestamp'=>$one['source']['timestamp'],
			   'name'=>$one['source']['nick'],
			   'text'=>$one['source']['text'],
			   'count'=>$one['source']['count'],
			   'mcount'=>$one['source']['mcount'],
			   'geo'=>$one['source']['geo'],
			   'image'=>$image,
			   'image_large'=>$image_large,
			   'profile_url'=>$one['source']['name'],
			   'uid'=>$one['source']['name'],
		       'type'=>$one['source']['type']
			)
		 );
	  }
   }
   
   $count=count($result);
   $firsttime=$result[0]['timestamp'];
   $fid=$result[0]['id'];
   $lid=$result[$count-1]['id'];
   $lasttime=$result[$count-1]['timestamp'];
   $_SESSION['tx']['at_firsttime']=$firsttime;
   $_SESSION['tx']['at_lasttime']=$lasttime;
   $_SESSION['tx']['at_fid']=$fid;
   $_SESSION['tx']['at_lid']=$lid;
   return $t;
}
function update($type='',$op=0){
   $api='https://open.t.qq.com/api/info/update';
   $cans=array(
      'format'=>'json',
	  'op'=>$op,//0：仅获取数据更新的条数；1：获取完毕后将相应的计数清零。
	  'type'=>$type
   );//type 5：首页未读消息计数；
     //           6：@页未读消息计数；
     //           7：私信页未读消息计数；
     //           8：新增听众数；
     //           9：首页新增的原创广播数。
     //           op=0时不输人type，返回所有类型计数；
     //           op=1时需输入type，返回所有类型计数，同时清除该type类型的计数。
   
   $param=$this->param($cans);
   $result=request($api.'?'.$param);
   $result=json_decode($result,1);
   if($result['ret']!=0) return false;
   return $result;
}
function info(){
   $api='https://open.t.qq.com/api/user/info';
   $cans=array(
      'format'=>'json'
   );
   $param=$this->param($cans);
   $result=request($api.'?'.$param);
   $data=json_decode($result,true);
   $data=$data['data'];
   $_SESSION['tx']['nick']=$data['nick'];
   $_SESSION['tx']['name']=$data['name'];
   return $data;
}
function refresh_token($refresh_token){
   $cans=array(
      'client_id'=>$this->appid,
	  'grant_type'=>'refresh_token',
	  'refresh_token'=>$refresh_token
   );
   foreach($cans as $key=>$val){
      $a[].=$key.'='.$val;
   }
   $param=join('&',$a);
   $api='https://open.t.qq.com/cgi-bin/oauth2/access_token';
   $result=request($api.'?'.$param);
   $result=explode('&',$result);
   foreach($result as $val){
      $r=explode('=',$val);
      $k[].=$r[0];
      $v[].=$r[1];
   }
   $info=array_combine($k,$v);
   $this->access_token=$info['access_token'];
   $_SESSION['tx']['access_token']=$info['access_token'];
   $_SESSION['tx']['refresh_token']=$info['refresh_token'];
   $_SESSION['tx']['openid']=$info['openid'];
}
function tweet($content,$longitude=0,$latitude=0){//先经度后纬度
   $api='https://open.t.qq.com/api/t/add';
   $cans=array(
	  'content'=>$content,
      'format'=>'json'
   );
   if($longitude && $latitude){
      $cans1=array(
	     'longitude'=>$longitude,
	     'latitude'=>$latitude
	  );
	  $cans = array_merge($cans,$cans1);
   }

   $param=$this->param($cans);
   return request($api.'?'.$param,'POST');
}
function timeline($pageflag=0,$pagetime=0,$reqnum=10,$type=0,$contenttype=0){
   $api='https://open.t.qq.com/api/statuses/home_timeline';
   $cans=array(
      'format'=>'json',
      'pageflag'=>$pageflag,//0：第一页，  向下翻页：1                   向上翻页：2
      'pagetime'=>$pagetime,//第一页：填0  向下翻页：最后一条记录时间    向上翻页：第一条记录时间
      'reqnum'=>$reqnum,//每次条数
	  'type'=>$type,//0x1 原创发表 0x2 转载 如需拉取多个类型请使用|，如(0x1|0x2)得到3，则type=3即可，0所有类型
	  'contenttype'=>$contenttype//内容过滤。0-表示所有类型，1-带文本，2-带链接，4-带图片，8-带视频，0x10-带音频 建议不使用contenttype为1的类型，如果要拉取只有文本的微博，建议使用0x80
   );
   $param=$this->param($cans);
   $result = json_decode(request($api.'?'.$param),1);
   $result=$result['data']['info'];
   foreach($result as $one){
      $image='';
	  $image_large='';
      if($one['type']==1){
	     if(isset($one['image'])){
		    $image=$one['image'][0];
			$image_large=$one['image'][0].'/460';
	     }
	     $t[]=array(
		    'id'=>$one['id'],
			'timestamp'=>$one['timestamp'],
			'head'=>$one['head'].'/',
			'name'=>$one['nick'],
			'text'=>$one['text'],
			'count'=>$one['count'],
			'mcount'=>$one['mcount'],
			'geo'=>$one['geo'],
			'image'=>$image, 
			'image_large'=>$image_large,
			'profile_url'=>$one['name'],
			'uid'=>$one['name'],
			'plat'=>'tx',
			'type'=>$one['type']
		 );
	  }elseif($one['type']==2){
	     if(isset($one['source']['image'])){
		    $image=$one['source']['image'][0];
			$image_large=$one['source']['image'][0].'/460';
		 }
		 $t[]=array(
		 	'id'=>$one['id'],
			'timestamp'=>$one['timestamp'],
			'head'=>$one['head'].'/',
			'name'=>$one['nick'],
			'text'=>$one['text'],
			'count'=>$one['count'],
			'mcount'=>$one['mcount'],
			'geo'=>$one['geo'],
			'image'=>$image,
			'profile_url'=>$one['name'],
			'uid'=>$one['name'],
			'plat'=>'tx',
			'type'=>$one['type'],
			'source'=>array(
		       'id'=>$one['source']['id'],
			   'timestamp'=>$one['source']['timestamp'],
			   'head'=>$one['source']['head'].'/',
			   'name'=>$one['source']['nick'],
			   'text'=>$one['source']['text'],
			   'count'=>$one['source']['count'],
			   'mcount'=>$one['source']['mcount'],
			   'geo'=>$one['source']['geo'],
			   'image'=>$image, 
			   'image_large'=>$image_large,
			   'profile_url'=>$one['source']['name'],
			   'uid'=>$one['source']['name'],
		       'type'=>$one['source']['type']
			)
		 );
	  }
   }
   return $t;
}
function tl_list($fanye=0){
   switch ($fanye) {
    case 1://向后
        $pageflag=1;
		$pagetime=$_SESSION['tx']['lasttime'];
        break;
    case -1://向前
        $pageflag=2;
		$pagetime=$_SESSION['tx']['firsttime'];
        break;
    default:
        $pageflag=0;
		$pagetime=0;
   }
   $host='http://t.qq.com/';
   $li='<ul>';
   $result = $this->timeline($pageflag,$pagetime);
   foreach($result as $t){
	  $img='';
      if($t['type']==1){
	     if($t['image'])$img='<br /><img src='.$t['image'][0].'/>';
         $tweet="<li><div id='t$t[id]' class='msgbox'><a href='$host$t[name]' class='nick'>$t[nick]</a>：
   	            $t[text]$img";
      }elseif($t['type']==2){
	     if($t['source']['image'])$img='<br /><img src='.$t['source']['image'][0].'/>';
         $tweet="<li><div id='t$t[id]' class='msgbox'><a href='$host$t[name]' class='nick'>$t[nick]</a>：$t[text]
		        <div class='msgbox'><a href='$host". $t['source']['name'] ."' class='nick'>".$t['source']['nick'].
				"</a>：".$t['source']['text']."$img<div class='pubinfo'><a target=_blank href=http://t.qq.com/p/t/".$t['source']['id']." class='zp'>转".$t['source']['count'].'评'.$t['source']['mcount']."</a></div></div>";
      }else continue;
      $li.=$tweet."<div class='pubinfo'><a target=_blank href=http://t.qq.com/p/t/".$t['id']." class='zp'>转$t[count]评$t[mcount]</a><span class='zhuanping'><a href=home.php?like=$t[id]>+1</a> <span onclick=zhuanping($t[id],0,'tx')>转播</span> <span onclick=zhuanping($t[id],1,'tx')>评论</span></span></div></div></li>";
	}
   $firsttime=$result[0]['timestamp'];
   $count=count($result);
   $lasttime=$result[$count-1]['timestamp'];
   $_SESSION['tx']['firsttime']=$firsttime;
   $_SESSION['tx']['lasttime']=$lasttime;
   return $li.'</ul>';
}
function param($para){
   $cans=array(
      'oauth_consumer_key'=>$this->appid,
	  'access_token'=>$this->access_token,
	  'openid'=>$_SESSION['tx']['openid'],
	  'clientip'=>$_SERVER["REMOTE_ADDR"],
	  'oauth_version'=>'2.a',
	  'scope'=>'all'
   );
   if(is_array($para)) $cans = array_merge($cans,$para);//新旧参数数组合并
   ksort($cans);//参数数组排序
   foreach($cans as $key=>$val){
      $a[].=$key.'='.urlencode($val);
   }
   $param=join('&',$a);
   return $param;
}
function pic_tweet($content,$pic){
   $api='https://open.t.qq.com/api/t/add_pic';
   $api2='http://127.0.0.1/t/output.php';
   $cans=array(
	  'content'=>$content,
      'format'=>'json',
	  'pic'=>'file'
   );


   $param=$this->param($cans);
   return request2($api2,'POST',$pic,$this->appid,$this->access_token);
}
}

function sina_param($new_canshu=array()){//URL参数合成
   $canshu=array();
   if(isset($_SESSION['sina']['access_token'])){
      $canshu=array(
         'access_token'=>$_SESSION['sina']['access_token']
      );
   }
   if(is_array($new_canshu)) $canshu = array_merge($canshu,$new_canshu);
   foreach($canshu as $key=>$val){
      $a[].=$key.'='.$val;
   }
   $param=join('&',$a);
   return $param;
}
function request($url,$method='GET'){//GET HTTPS
   $ch = curl_init(); 
   curl_setopt($ch, CURLOPT_URL,$url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   if($method=='POST'){
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $url);
   }else{
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
   }
   return curl_exec($ch);
}
function request2($url,$method='GET',$data,$appid,$access_token){//GET HTTPS
   /*
   $ch = curl_init(); 
   curl_setopt($ch, CURLOPT_URL,$url);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   curl_setopt($ch, CURLOPT_POST, 1);
   curl_setopt($ch, CURLOPT_POSTFIELDS, $url);
   $output = curl_exec($ch);
   curl_close($ch);
   echo $output;
   */
   
   $post_data = array(
      'oauth_consumer_key'=>$appid,
	  'access_token'=>$access_token,
	  'openid'=>$_SESSION['tx']['openid'],
	  'clientip'=>$_SERVER["REMOTE_ADDR"],
	  'oauth_version'=>'2.a',
	  'scope'=>'all',
	  'content'=>'烦烦烦烦啊!',
      'format'=>'json',
	  'pic'=>'t.gif',
      //"file"=>"@".dirname(__FILE__).'/t.gif'//$data//'http://127.0.0.1/t/t.gif'
	  'file' => array(
		 'type' => 'image/jpg',
		 'name' => '0.jpg',
		 'data' => file_get_contents($data)
	  )
   );
   //$pa=http_build_query($post_data);
   
   $ch = curl_init();
   
   
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   
   curl_setopt($ch, CURLOPT_POST, 1 );
   curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
   curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE );
   curl_setopt($ch, CURLOPT_URL, $url);
   $output = curl_exec($ch);
   curl_close($ch);
   echo $output;
}
function to_list($info){
   $li='<ul>';
   foreach($info as $t){
      $img='';
      if($t['plat']=='tx'){
	     $name_link='http://t.qq.com/'.$t['profile_url'];
	  }else{
	     $name_link='http://www.weibo.com/'.$t['profile_url'];
	  }
      if($t['type']==1){//原创
	     
	     if($t['image'])$img='<br /><img class="wbimg" onclick="sizeSwitch(this)" another='.$t['image_large'].' src='.$t['image'].'/>';
         $tweet="<li><div id='t$t[id]' class='msgbox $t[plat]'><a href='$name_link' target='_blank' class='nick'><img class='headpic' alt='头像' src='$t[head]'/>$t[name]</a>：$t[text]$img";
      }elseif($t['type']==2){//转播
	     if($t['plat']=='tx'){
	        $re_name_link='http://t.qq.com/'.$t['source']['profile_url'];
	     }else{
	        $re_name_link='http://www.weibo.com/'.$t['source']['profile_url'];
	     }
         if($t['source']['image'])$img='<br /><img class="wbimg" onclick="sizeSwitch(this)" another='.$t['source']['image_large'].' src='.$t['source']['image'].'/>';
         $tweet="<li><div id='t$t[id]' class='msgbox $t[plat]'><a href='$name_link' target='_blank' class='nick'><img class='headpic' alt='头像' src='$t[head]'/>$t[name]</a>：$t[text]
		           <div class='msgbox'><a href='".$re_name_link."' class='nick'>".$t['source']['name'].
				   "</a>：".$t['source']['text']."$img<div class='pubinfo'><a target=_blank href=#"." class='zp'>转".$t['source']['count'].'评'.$t['source']['mcount']."</a></div></div>";
      }else continue;
         if(isset($t['source']['comments'])&&($t['source']['comments']==1)) $li.=$tweet."<div class='pubinfo'><a target=_blank href=#"." class='zp'>转$t[count]评$t[mcount]</a><span class='zhuanping'><span onclick=zhuanping($t[id],1,'$t[plat]',".$t['source']['id'].")>回复</span></span></div></div></li>";//是评论列表
		 else $li.=$tweet."<div class='pubinfo'><a target=_blank href=#"." class='zp'>转$t[count]评$t[mcount]</a><span class='zhuanping'><a href=home.php?plat=$t[plat]&like=$t[id]>+1</a> <span onclick=zhuanping($t[id],0,'$t[plat]')>转播</span> <span onclick=zhuanping($t[id],1,'$t[plat]')>评论</span></span></div></div></li>";
      }
   return $li.'</ul>';
}
function order($a=array()){
   $linshi=array();
   $count=count($a);
   for($i=0;$i<$count-1;$i++){
      for($j=0;$j<$count-1-$i;$j++){
         if($a[$j]['timestamp']<$a[$j+1]['timestamp']){
	        $linshi=$a[$j];
		    $a[$j]=$a[$j+1];
		    $a[$j+1]=$linshi;
         } 
	  }
   }
   return $a;
}

?>