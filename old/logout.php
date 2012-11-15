<?php
error_reporting(E_ALL);
require 'file_info.php';

if (!filter_has_var(INPUT_GET,'id')) {
	echo "Need id";
	return;
}
$id = $_GET['id'];

get_CB_info($address,$port);
$sock = fsockopen ($address, $port, $errno, $errstr); 
if (!$sock) { 
	echo "Error: could not open socket connection becuase of $errno and $errstr";
	return;
}
fwrite($sock, "10".$id);
socket_close($sock);
?>