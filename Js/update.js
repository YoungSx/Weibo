var xmlhttp;
function loadDoc(url){
   /*
   if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
      xmlhttp=new XMLHttpRequest();
   }else{// code for IE6, IE5
      xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
   }
   */
   xmlhttp=new XMLHttpRequest();
   xmlhttp.onreadystatechange=function(){
      if (xmlhttp.readyState==4 && xmlhttp.status==200){
	  //return xmlhttp.responseText;
      }
   }
   xmlhttp.open("GET",url,false);
   xmlhttp.send();
   return xmlhttp.responseText;
}
function updata(){
   content=loadDoc("..//update.php");
   self.postMessage(content);
}
self.setInterval("updata()",30000);