var xmlhttp;
function loadDoc(url){
   xmlhttp=new XMLHttpRequest();
   xmlhttp.onreadystatechange=function(){
      if (xmlhttp.readyState==4 && xmlhttp.status==200){
      }
   }
   xmlhttp.open("GET",url,false);
   xmlhttp.send();
   return xmlhttp.responseText;
}
content=loadDoc("..//tweet.php");
self.postMessage(content);