<?php
error_reporting(E_ALL);
require 'file_info.php';
set_time_limit(120);
ignore_user_abort(true);

function move_socket_data(&$src, &$dst, &$fdlog = NULL) {
	while (true) {
		$data = socket_read($src, 4096, PHP_BINARY_READ);
		if ($data === false) return "src: ".socket_strerror(socket_last_error($src));
		if ($data === "") return true;
		if ($fdlog !== NULL) fwrite($fdlog,"\tReceived: ".$data."\n");
		while (strlen($data) > 0) {
			$bw = socket_write($dst, $data);
			if ($bw === false) return socket_strerror(socket_last_error($dst));
			if ($bw === 0) return "Wrote 0 bytes to dst";
			$data = substr($data, $bw);
		}
	}
}



$fdlog = fopen("./info/DEBUG_SPAWNER.txt",'a');
if ($fdlog === false) {
	echo 'Cannot open log file';
}
fwrite($fdlog, "NEW SESSION -- ".date("m-d-H:i.s")." \n");

// get info
$cc_port = $_GET['port'];
$cc_address = $_SERVER['REMOTE_ADDR'];
get_CB_info($ds_address,$ds_port);

// make connections
fwrite($fdlog, "DS Attempting connect to ".$ds_address.":".$ds_port." ");
//$ds_sock = fsockopen($ds_address, $ds_port, $errno, $errstr); 
//$ds_sock = stream_socket_client("tcp://".$ds_address.":".$ds_port, $errno, $errstr);
$ds_sock = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
$ret = socket_connect($ds_sock, $ds_address, $ds_port);
if (!$ret) {
    //echo "ERROR for DS $ds_address: $errno - $errstr<br />\n";
	echo "ERROR for DS $ds_address: ".socket_strerror(socket_last_error($ds_sock));
	fwrite($fdlog, "ERROR for DS $ds_address: ".socket_strerror(socket_last_error($ds_sock)));
	return;
} 
fwrite($fdlog, "OK\n");
fwrite($fdlog, "CC Attempting connect to ".$cc_address.":".$cc_port." ");
//$cc_sock = stream_socket_client("tcp://".$cc_address.":".$cc_port, $errno, $errstr);
$cc_sock = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
$ret = socket_connect($cc_sock, $cc_address, $cc_port);
if (!$ret) {
    //echo "ERROR for CC $cc_address: $errno - $errstr<br />\n";
	echo "ERROR for CC $ds_address: ".socket_strerror(socket_last_error($cc_sock));
	fwrite($fdlog, "ERROR for CC $ds_address: ".socket_strerror(socket_last_error($cc_sock)));
	return;
}
fwrite($fdlog, "OK\n");

// make nonblocking
$ret = socket_set_nonblock($ds_sock);
if ($ret === false) { echo 'Cannot make DS nonblocking';  fwrite($fdlog,'Cannot make DS nonblocking'); return; }
$ret = socket_set_nonblock($cc_sock);
if ($ret === false) { echo 'Cannot make CC nonblocking';  fwrite($fdlog,'Cannot make CC nonblocking'); return; }

do {
	// select which socket wants to say something
	$read = array($ds_sock, $cc_sock);
	$write = NULL;
	$except = NULL; // exceptions
	fwrite($fdlog, "about to socket_select... ");
	$num_mod = socket_select($read, $write, $except, NULL); // pass by reference
	if ($num_mod === false) {
		echo "socket_select() failed, reason: ".socket_strerror(socket_last_error())."\n";
		fwrite($fdlog, "socket_select() failed, reason: ".socket_strerror(socket_last_error())."\n");
		return;
	}
	fwrite($fdlog, "Returned ".$num_mod."\n");

	// what did we select?
	if (in_array($ds_sock, $read)) {
		fwrite($fdlog, "Moving socket data from DS to CC\n");
		$ret = move_socket_data($ds_sock, $cc_sock, $fdlog);
	} else if (in_array($cc_sock, $read)) {
		fwrite($fdlog, "Moving socket data from CC to DS\n");
		$ret = move_socket_data($cc_sock, $ds_sock, $fdlog);
	} 
} while ($ret !== true);

echo "\t!! ".$ret."\n";
fwrite($fdlog, "\t!! ".$ret."\n");


fclose($ds_sock);
fclose($cc_sock);
fclose($fdlog);
?>