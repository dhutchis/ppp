<?php
error_reporting(E_ALL);
require 'file_info.php';

$name = $_GET["n"];
$cl_address = $_GET["a"];
$cl_port = $_GET["p"];

// filter vars
if (
		strlen($name) > $NAME_MAX_LENGTH
		or FALSE === filter_var($cl_address,FILTER_VALIDATE_IP) // any IP okay for now
		or FALSE === filter_var($cl_port,FILTER_VALIDATE_INT,array("options"=>
						array("min_range"=>1, "max_range"=>65535)))
	)
	die("invalid parameters");

get_CB_info($name, $ds_address, $ds_port);
if ($ds_address === NULL or $ds_port === NULL)
	die("\nCannot lookup $name");

// echo "\nds_address: $ds_address\nds_port: $ds_port";
// echo "\ncl_address: $cl_address\ncl_port: $cl_port";
// $str = "./helloworld.exe $ds_address $ds_port $cl_address $cl_port";
// echo "\nstr: $str";

exec("./ppp.exe $ds_address $ds_port $cl_address $cl_port", $res);
print_r($res);

?>