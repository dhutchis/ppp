<?php
$dirname = "info";
$filename_CBinfo = ($dirname == null ? "." : $dirname) . "/back_info.txt";

// parameters are by reference
function get_CB_info(&$address, &$port) {
	global $dirname, $filename_CBinfo;
	if (file_exists($filename_CBinfo)) {
		$fh = fopen($filename_CBinfo, 'r') or die("Cannot open $filename_CBinfo");
		$address = trim(fgets($fh));
		$port = trim(fgets($fh)); // string or int?
		fclose($fh);
	}
}
// call get_CB_info($address,$port);

function get_and_incr_idcount() {
	global $dirname;
	$filename_idcount = ($dirname == null ? "." : $dirname) . "/idcount.txt";
	if (file_exists($filename_idcount)) {
		$fh = fopen($filename_idcount, 'r+') or die("Cannot open $filename_idcount");
		$idcnt = fread($fh,5);
		if (!filter_var($idcnt, FILTER_VALIDATE_INT, array("options"=>
						array("min_range"=>10000, "max_range"=>99999)))) {
			return 'Bad idcnt';
		}
		rewind($fh); //fseek($fh,0,SEEK_SET);
	} else {
		$idcnt = '10000';
		$fh = fopen($filename_idcount, 'w') or die("Cannot open $filename_idcount");
	}
	$newcnt = intval($idcnt)+1;
	if ($newcnt >= 100000)
		$newcnt = 10000;
	fwrite($fh,strval($newcnt));
	fclose($fh);
	return $idcnt;
}
?>