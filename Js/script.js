var old,boxOpen;
//window.addEventListener('deviceorientation', update, true);
var lastTime=0,v=0,last=1;
function orientation(event){
   var interval=30;//间隔时间
   var deg=event.beta-20;
   var beta=Math.round(deg-20)*3.14/180;
   var a=Math.sin(beta)*9.82*15;//用倾斜角度求加速度(*10因为加速度太小)
   if(((last>0) && (a<0) ) || ((last<0) && (a>0))) v=0;
   last=a;
   v=v+a*(interval/1000);//用加速度求速度（不确定对）
   var curTime = new Date().getTime();
   if( (curTime-lastTime>interval) && ( (deg<-0) || (deg>15))  ){//留出静止的一定角度以便用户进行操作。
      if(document.body.scrollTop>=0 || beta>=0) scroll(0,document.body.scrollTop+v+deg);
   }
   lastTime=curTime;
}
function zhuanping(id,cz,plat,xid){
   box=document.getElementById('t'+id);
   if(!boxOpen){
      boxOpen=true;
      old=box.innerHTML;
	  if(!xid) xid='';
      switch(cz)
      {
      case 0:
       botton='转播';
       break;
      case 1:
       botton='评论';
       break;
      default:
       botton='发表';
      }
      box.innerHTML+=
      "<div id='zpbox'><form action='home.php' method='post'>"+
      "<textarea name='zpcont' style='width:100%;'></textarea>"+
	  "<input type='hidden' name='plat' value='"+plat+"'/><input type='hidden' name='xid' value='"+xid+"'/><input type='hidden' name='cz' value='"+cz+"'/><input type='hidden' name='id' value='"+id+"'/><div class='bar'><input type='submit' value='"+botton+"'/>"
      +"</div></form></div>";
   }else{
      boxOpen=false;
	  //box.innerHTML=old;
	  document.getElementById("zpbox").parentNode.removeChild(document.getElementById("zpbox")); 
   }
}
function yc(id){
   box=document.getElementById(id);//box.style.display='none'
   if(box) setTimeout("box.style.display='none'",2000);
}
if(navigator.geolocation){
   //alert('支持定位！');
   //navigator.geolocation.getCurrentPosition(info,cuowu);
}else{
   //alert('不支持定位！');
}
function info(position){
   a=document.getElementById('longitude');
   b=document.getElementById('latitude');
   c=document.getElementById('local');
   if(c.value=='未定位'){
      a.value=position.coords.longitude;
      b.value=position.coords.latitude;
      c.value='已定位';
      c.style.background='orange';
   }else{
      c.value='未定位';
	  c.style.background='red';
   }
}
function cuowu(){

}
var xmlhttp;
function loadDoc(url){
   if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
   }else{// code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
   }
   xmlhttp.onreadystatechange=function(){
      if (xmlhttp.readyState==4 && xmlhttp.status==200){
	  //return xmlhttp.responseText;
      }
   }
   xmlhttp.open("GET",url,false);
   xmlhttp.send();
   return xmlhttp.responseText;
}
///////////////////////////////////////////////
function back(e){
   document.getElementById("message").style.display="none";
   document.getElementById('head').style.display="block";
   document.getElementById('refresh').style.display="block";
   document.getElementById('maintimeline').style.display="block";
   document.getElementById("message").innerHTML="加载中...";
   e.style.display="none";
   
}
var message;
function messageBox(type){
   msgbox=document.getElementById("message");
   document.getElementById('head').style.display="none";
   document.getElementById('refresh').style.display="none";
   document.getElementById('maintimeline').style.display="none";
   document.getElementById('back').style.display="block";
      //console.log(msgbox.style.display);
      //msgbox.innerHTML="";
      //msgbox.style.display='none';
   message=new Worker("Js/message.js");
   message.postMessage(type);
   msgbox.style.display="block";
   message.onmessage=function(event){
      if(event.data){
	     msgbox.innerHTML=event.data; 
	  }
   }
}

/////////////////////////////////////////////////////////////
var friendsList;
function friends(type){
   friendsbox=document.getElementById("message");
   document.getElementById('head').style.display="none";
   document.getElementById('refresh').style.display="none";
   document.getElementById('maintimeline').style.display="none";
   document.getElementById('back').style.display="block";
      //console.log(msgbox.style.display);
      //msgbox.innerHTML="";
      //msgbox.style.display='none';
   friendsList=new Worker("Js/friends.js");
   friendsList.postMessage(type);
   friendsbox.style.display="block";
   friendsList.onmessage=function(event){
      if(event.data){
	     friendsbox.innerHTML=event.data; 
	  }
   }
}
/////////////////////////////////////////////////////////////
var timeline;
function getTimeline(){
   document.getElementById("refresh").innerHTML="载入中...";
   //content=loadDoc("timeline.php");
   timeline=new Worker("Js/timeline.js");
   timeline.onmessage=function(event){
      if(event.data){
         document.getElementById("maintimeline").innerHTML=event.data;
		 document.getElementById("refresh").style.display='none';
      }
   }
   
}
///////////////////////////////////////////////
var update=new Worker("Js/update.js");
var noticontent;
update.onmessage=function(event){
   if(event.data){
  	  var obj=eval('('+event.data+')');
		//console.log(event.data);
      document.getElementById('tx_new_fans_num').innerHTML=obj.tx.new_fans_num;
      document.getElementById('tx_new_at_num').innerHTML=obj.tx.new_at_num;
      document.getElementById('sina_new_fans_num').innerHTML=obj.sina.new_fans_num;
      document.getElementById('sina_new_at_num').innerHTML=obj.sina.new_at_num;
      document.getElementById('sina_cmt').innerHTML=obj.sina.cmt;
	  var sinaContent='',txContent='';
	  if(obj.tx.new_fans_num)  txContent+='新听众' + obj.tx.new_fans_num + ' ';
	  if(obj.tx.new_at_num)  txContent+='新@' + obj.tx.new_at_num + ' ';
	  if(txContent) txContent='腾讯:'+txContent;
	  
	  if(obj.sina.new_fans_num)  sinaContent+='新粉丝' + obj.sina.new_fans_num + ' ';
	  if(obj.sina.new_at_num)  sinaContent+='新@' + obj.sina.new_at_num + ' ';
	  if(obj.sina.cmt)  sinaContent+='新评论' + obj.sina.cmt + ' ';
	  if(sinaContent) sinaContent='新浪:'+sinaContent;
	  
	  if((txContent || sinaContent)&&(noticontent!=(txContent+sinaContent))){
	     noticontent=txContent+sinaContent;
	     notification(noticontent);
	  }
      console.log(obj.status);
      if(obj.status != '0'){
         document.getElementById('refresh').innerHTML='有'+obj.status+'条新微博';
         document.getElementById('refresh').style.display='block';
      }
   }
}
//////////////////////////////////////////////////////
function Sswitch(e){
   if(e.className=="switch"){
	  e.className="switch2";
      window.addEventListener('deviceorientation', orientation, true);
   }else{
      e.className="switch";
      window.removeEventListener('deviceorientation', orientation, true);
   }
}
////////////////////////////////////////////////////////////
function sizeSwitch(e){
   var temp=e.getAttribute("another");
   e.setAttribute("another",e.src);
   e.src=temp;
}
////////////////////////////////////////////////////////////////
function RequestPermission(callback){
   window.webkitNotifications.requestPermission(callback);	  
}
function notification(body,title,icon){
   if(window.webkitNotifications.checkPermission()>0){
      RequestPermission(notification);
   }
   if(!icon) icon='Image/Ysx.ico';
   if(!title) title='你有新消息 - Ysx版微博';
   var popup=window.webkitNotifications.createNotification(icon,title,body);
       popup.show();
 	   setTimeout(
 	      function(){
 		     popup.cancel();
 		  },
		  '10000'
	   );
}
////////////////////////////////////////////////////////////
function apro_onclick(e){
   if(e.style.position!="static"){
      if(e.style.top=="-130px"){
	     e.style.top="0px";
	  }else e.style.top="-130px";
   }
}
