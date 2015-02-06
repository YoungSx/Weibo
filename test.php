<?php
echo <<<EOT
<!DOCTYPE html>
<html>
 <head>
  <link href="Style/style.css" rel='stylesheet' type='text/css'/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=no"/>
  <title>Ysx Home</title>
  <script type="text/javascript" src="/Js/script.js">
  </script>
 </head>
 <body onload="yc('tips')">
  <div id="main">
  <div id='title'><a href=home.php>Ysx Home</a></div>
EOT;


require('cfg.php');

echo <<<AAA

  $tips
  <div id='head'>
  $_SESSION[nick](@$_SESSION[name])<br />
  <a href='fans.php?type=0&page=0'>听众:$fansnum$new_fans_num</a> <a href='fans.php?type=1&page=0'>收听:$idolnum</a> <a href='at.php?page=0'>@提到我的$new_at_num</a>
  <form action='home.php' method='post'>
   <textarea name='tw'></textarea><div class="bar">
   <input type="button" onclick="navigator.geolocation.getCurrentPosition(info,cuowu);" id='local' value="定位"/>
   <input type="hidden" value="" id="latitude" name="latitude"/><input value="" type="hidden" id="longitude" name="longitude"/>
   <input type="submit" value="发微博！"/></div>
  </form>
  </div>
  <div id='timeline'>
  $timeline
  <div class=fanye><a href=index.php?page=-1>&lt;</a> 翻页 <a href=index.php?page=1>&gt;</a></div>
  </div>
  </div>
  </body>
</html>
AAA;

?>