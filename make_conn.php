<?php
error_reporting(E_ALL);
require 'file_info.php';

$name = $_GET["n"];
//$cl_address = $_GET["a"];
$cl_address = $_SERVER["REMOTE_ADDR"];
$cl_port = $_GET["p"];

// filter vars
if (
		strlen($name) > $NAME_MAX_LENGTH
		//or FALSE === filter_var($cl_address,FILTER_VALIDATE_IP) // any IP okay for now
		or FALSE === filter_var($cl_port,FILTER_VALIDATE_INT,array("options"=>
						array("min_range"=>1, "max_range"=>65535)))
	)
	die("invalid parameters\n");

get_CB_info($name, $ds_address, $ds_port);
if ($ds_address === NULL or $ds_port === NULL)
	die("\nCannot lookup $name\n");

// echo "\nds_address: $ds_address\nds_port: $ds_port";
// echo "\ncl_address: $cl_address\ncl_port: $cl_port";
// $str = "./helloworld.exe $ds_address $ds_port $cl_address $cl_port";
// echo "\nstr: $str";

// no output = will asynchronously execute
echo "OK EXECing\n";
exec("./ppp.exe ".escapeshellarg($ds_address)." ".escapeshellarg($ds_port)." ".escapeshellarg($cl_address)." ".escapeshellarg($cl_port)." &> /dev/null &", $res);
print_r($res);

?>