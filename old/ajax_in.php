<?php
error_reporting(E_ALL);
require 'file_info.php';


// ***************** MAIN ***************
set_time_limit(20);

/*if (!filter_has_var(INPUT_POST,'id') or !filter_has_var(INPUT_POST,'data')) {
	echo 'Need id and data';
	return;
}
$id = $_POST['id'];
// enforce $id is 5 digits long
if (!filter_var($id, FILTER_VALIDATE_INT, array("options"=>
				array("min_range"=>10000, "max_range"=>99999)))) {
	echo 'Bad id';
	return;
}
$data = $_POST['data'];
*/
$id = $_REQUEST['id'];
$data = $_REQUEST['data'];

get_CB_info($address,$port);
$sock = fsockopen ($address, $port, $errno, $errstr); 
if (!$sock) { 
	echo "Error: could not open socket connection to $address on port $port becuase of $errno and $errstr";
	return;
}
// should I use register_shutdown_function() to close socket in case user aborts?

fwrite($sock, "00".$id.$data."\n\n");
$resp = fread($sock,2);
fclose($sock);
if (strcmp($resp,'OK') == 0) {
	echo 'OK';
} else {
	echo 'BC returned: '.$resp;
}

?>
