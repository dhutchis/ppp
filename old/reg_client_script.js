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
	if (typeof(EventSource)!=="undefined") {
		var source=new EventSource("sse_out.php?id="+"<?php echo $id; ?>");
		source.onmessage=function(event) {
			document.getElementById("SSEoutput").innerHTML+=event.data + "\n";
		};
	} else {
		document.getElementById("SSEoutput").innerHTML = 
			'Your browser does not support server-sent events.';
	}
};

