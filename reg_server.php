<?php
error_reporting(E_ALL);
require 'file_info.php';

$ds_name = $_GET["n"];
$ds_address = $_GET["a"];
$ds_port = $_GET["p"];

// filter vars
if (
		strlen($ds_name) > $NAME_MAX_LENGTH
		or FALSE === filter_var($ds_address,FILTER_VALIDATE_IP) // any IP okay for now
		or FALSE === filter_var($ds_port,FILTER_VALIDATE_INT,array("options"=>
						array("min_range"=>0, "max_range"=>65535)))
	)
	die("invalid parameters");
echo "<br/>n: $ds_name\n";
echo "<br/>a: $ds_address\n";
echo "<br/>p: $ds_port\n";
put_CB_info($ds_name,$ds_address,$ds_port);

?>