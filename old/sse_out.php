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
///$fd = fopen(a.txt,'a');
fwrite($sock, "01".$id);
///fwrite($fd, "To CB: 01".$id);
// now setup SSE serve and perma-loop serving events
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
$data = fgets($sock);
	// returns false if socket closed or socket error

	echo 'data: read '.$data;
	echo "\n\n";
	flush(); ob_flush();
echo "data: test data\n\n";
flush(); ob_flush();
do
{
	$data = socket_read($sock,1024,PHP_NORMAL_READ); // line length 1024
	// returns false if socket closed or socket error

	echo 'data: '.$data;
	echo "\n\n";
	flush(); ob_flush();
	
} while ($data !== false);

echo "data: about to close socket";
flush(); ob_flush();
socket_close($sock);
///fclose($fd);
?>