<?php
session_start();
if(isset($_SESSION['tx']['access_token']) && isset($_SESSION['sina']['access_token'])){
   header('Location:home.php');
}
?>
<html>
 <head>
  <meta name="viewport" content="width=371, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes"/>
  <title>Ysx版微博</title>
  <style>
   html,body{
    margin:0px;
	background:#128ABC;
   }
   #main{
    width: 371px;
    margin: 0px auto;
	color:white;
	text-shadow:3px 3px 5px black;
   }
   a{
	color:white;
	text-decoration:none;
   }
   #title{
    width:110px;
	margin:0px auto;
	font-size:24px;
	color:orange;
   }
   #login{
    float:right;
    width:100px;
	padding:5px;
	margin:10px;
	background:#1ABDE6;
	border:2px #1169EE solid;
	border-radius:5px;
    cursor:pointer;
	text-shadow:0px 0px 5px black;
   }
   #login:hover{
    box-shadow:0px 0px 5px pink;
   }
   #login:active{
    box-shadow:0px 0px 5px red;
   }
   #des{
    text-indent:2em;
	margin:5px;
	text-shadow:1px 1px 5px black;
   }
  </style>
 </head>
 <body>
  <div id="main">
   <div id="title">Ysx版微博</div>
   <div id="des">一个微博聚合网站，聚合腾讯、新浪微博的内容，享受便捷的体验，从此不要两边跑，两边发，爽乎！<br />内含重力操纵功能，手机稍倾斜就可滚动页面，登陆后可以在页面左下角看到此开关。</div>
   <div id="login"><a href="home.php">微博帐号登陆</a></div>
  </div>
 </body>
</html>