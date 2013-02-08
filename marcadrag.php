<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
"http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>hola</title>

<script src="js/jquery-1.3.2.min.js" type="text/javascript" language="javascript"></script>

</head>
<body>

<script type="text/javascript" charset="utf-8">


jQuery(function ($) {
	$.fn.radiodrag = function (opt) {
	
		var element = this;
		var pressed=0;
		function concatObject(obj) {
			str='';
			for(prop in obj){
				str+=prop + " value :"+ obj[prop]+"\n";
			}
			var txt=document.getElementById("south")
			txt.innerHTML='<pre>'+str+'</pre>';
			//alert(str);
		}
		
		function init()
		{
			
			$(element).mousedown(function(){
				pressed=1;
				//alert(pressed);
	
    		});
			$(document).mouseup(function(){
      			pressed=0;
				//alert(pressed);
    		});
			
			$(element).find('input').mousedown(function(){
				
				$(this).attr("checked",true);
					var txt=document.getElementById("south")
					txt.innerHTML='<pre>'+this.value+'</pre>';
					
			});
			
			$(element).find('input').mouseover(function(){
				
				if (pressed==1){
					$(this).attr("checked",true);
					var txt=document.getElementById("south")
					txt.innerHTML='<pre>'+this.value+'</pre>';	
				};//jQuery(this).addClass("menu_"+i);
				
				//alert(this.value)
			});
		}
		init();
		return this;
	};
});



$("#hola").ready(function(){
	$("#hola").radiodrag();
});
</script>
<form id='hola'>
<input name="radio1" type="radio" value="1">
<input name="radio1" type="radio" value="2">
<input name="radio1" type="radio" value="3">
<input name="radio1" type="radio" value="4">
<input name="radio1" type="radio" value="5">
<br />
<input name="radio2" type="radio" value="1">
<input name="radio2" type="radio" value="2">
<input name="radio2" type="radio" value="3">
<input name="radio2" type="radio" value="4">
<input name="radio2" type="radio" value="5">
<br />
<input name="radio3" type="radio" value="1">
<input name="radio3" type="radio" value="2">
<input name="radio3" type="radio" value="3">
<input name="radio3" type="radio" value="4">
<input name="radio3" type="radio" value="5">
<br />
<input name="radio4" type="radio" value="1">
<input name="radio4" type="radio" value="2">
<input name="radio4" type="radio" value="3">
<input name="radio4" type="radio" value="4">
<input name="radio4" type="radio" value="5">
<br />
<input name="radio5" type="radio" value="1">
<input name="radio5" type="radio" value="2">
<input name="radio5" type="radio" value="3">
<input name="radio5" type="radio" value="4">
<input name="radio5" type="radio" value="5">
<br />
<input name="radio6" type="radio" value="1">
<input name="radio6" type="radio" value="2">
<input name="radio6" type="radio" value="3">
<input name="radio6" type="radio" value="4">
<input name="radio6" type="radio" value="5"><br />

</form>
<div id="south"></div>
</html>
