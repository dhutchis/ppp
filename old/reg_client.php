<?php
error_reporting(E_ALL);
require 'file_info.php';

$id = get_and_incr_idcount();
if (!filter_var($id, FILTER_VALIDATE_INT, array("options"=>
				array("min_range"=>10000, "max_range"=>99999)))) {
	echo "error: $id";
	return;
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252" />
<script type="text/javascript">

function sendAJAX(data)
{
	var xmlhttp;
	if (window.XMLHttpRequest) // code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	else // code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			document.getElementById("AJAXresp").innerHTML+=xmlhttp.responseText+"\n";
		}
	}
	var params = "id=" + "<?php echo $id; ?>" + "&data=" + encodeURIComponent(data);
	xmlhttp.open("POST","ajax_in.php",true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-length", params.length);
    xmlhttp.setRequestHeader("Connection", "close");
	xmlhttp.send(params);
	document.getElementById("AJAXresp").innerHTML+=">>"+params+"\n";
}

window.onload = function() {
	/*window.onbeforeunload = function(event){
	  event = event || window.event;
	  /*if(someCondition == someValue){
		return event.returnValue = "Are you sure you want to leave?  someCondition does not equal someValue..."
	  };* /
	  var xmlhttp;
		if (window.XMLHttpRequest) // code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		else // code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		/*xmlhttp.onreadystatechange=function()
		{
			if (xmlhttp.readyState==4 && xmlhttp.status==200) {
				document.getElementById("AJAXresp").innerHTML+=xmlhttp.responseText+"\n";
			}
		}* /
		xmlhttp.open("GET","logout.php?id="+"<?php echo $id; ?>",true);
		xmlhttp.send();
	};*/
	
	if (typeof(EventSource)!=="undefined") {
		var source=new EventSource("sse_out.php?id="+"<?php echo $id; ?>");
		source.onmessage=function(event) {
			document.getElementById("SSEoutput").innerHTML+=event.data + "\n";
		};
	} else {
		document.getElementById("SSEoutput").innerHTML = 
			'Your browser does not support server-sent events.';
	}
	document.getElementById("SSEoutput").innerHTML += "READY\n";
};

</script>
</head>
<body>
<h2>Your id is <?php echo $id; ?></h2>

<div>
<h3>AJAX Input &amp; Response</h3>
<!--<form onsubmit=>-->
	Server Response:
	<textarea id="AJAXresp"></textarea>
	<br/>
	Input:
	<input type="text" name="AJAXinput" id="AJAXinput" value="" />
	<button type="button" type="submit" onclick="sendAJAX(document.getElementById('AJAXinput').value); return false;">Send</button>
<!--</form>-->
</div>

<div>
<h3>Server Sent Event Output</h3>
<textarea id="SSEoutput"></textarea>
</div>

</body>
</html>