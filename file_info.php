<?php
$dirname = "info";
$filename_CBinfo = ($dirname == null ? "." : $dirname) . "/back_info.txt";
$NAME_MAX_LENGTH = 15;
$ADDRESS_LENGTH = 15;
$PORT_LENGTH = 5;
$LINE_LENGTH = $NAME_MAX_LENGTH+1+$ADDRESS_LENGTH+1+$PORT_LENGTH; // plus a '\n'

if ($dirname !== null and !is_dir($dirname))
  mkdir($dirname) or die("Cannot make directory".$dirname);
/*// use bool file_exists ( string $filename_CBinfo ) 
$pathinfo = pathinfo($filename_CBinfo);
echo "Dir name : $pathinfo[dirname]<br />\n";
$realpath = realpath($pathinfo[dirname]);
//echo "Real dir : "
echo $realpath;
//echo " <br />\n"
echo "<br />\nBase name: $pathinfo[basename] <br />\n";
echo "Extension: $pathinfo[extension] <br />\n";*/


// address and port parameters are by reference
// pass name, get back address and port (both NULL if name is not in the file)
// File format (each line): name address port
function get_CB_info($name, &$address, &$port) {
	global $dirname, $filename_CBinfo;
	$address = NULL;
	$port = NULL;
	if (file_exists($filename_CBinfo)) {
		$fh = fopen($filename_CBinfo, 'r') or die("Cannot open $filename_CBinfo");
		do {
			$line = fgets($fh);
			if ($line === FALSE)
				break;
			$linesplit = explode(" ", $line); //echo "\nlinesplit[0]: $linesplit[0]";
			$nameinfile = $linesplit[0];
		} while (strcmp($name, $nameinfile) !== 0);  // case sensitive
		if ($line !== FALSE) { // match!
			//echo "\nlinesplit: "; print_r($linesplit);
			for ($i = 1; strcmp($linesplit[$i],"")===0; $i++)
				;
			$address = $linesplit[$i];
			for ($i++; strcmp($linesplit[$i],"")===0; $i++)
				;
			$port = trim($linesplit[$i]);
			//echo "\naddress: $address\nport: $port";
		}
		fclose($fh);
	}
}

// returns a string padded with extra space
function pad_string_trailing_space($string, $numspace) {
	//echo "\npad ($numspace,";
	$numspace -= strlen($string);
	//echo "$numspace): $string to ";
	while ($numspace > 0) {
		$string .= ' ';
		$numspace -= 1;
	}
	//echo $string."|";
	return $string;
}

function create_DB_line($name, $address, $port) {
	global $NAME_MAX_LENGTH, $ADDRESS_LENGTH, $PORT_LENGTH;
	return pad_string_trailing_space($name,$NAME_MAX_LENGTH)." "
		.pad_string_trailing_space($address,$ADDRESS_LENGTH)." "
		.pad_string_trailing_space($port,$PORT_LENGTH);
}

function testfile() {
	global $dirname, $filename_CBinfo;
	/*$fh = fopen($filename_CBinfo, 'w') or die("Cannot open $filename_CBinfo");
	fwrite($fh,"abcd 2.3.4.5 2345\n1234 3.4.5.6 3456\npqrs 40.50.60.70 30000\nwxyz 4.5.6.7 4567");
	fclose($fh);*/
	
	/*$fh = fopen($filename_CBinfo, 'c+') or die("Cannot open $filename_CBinfo");
	$name = "1234";
	do {
		$line = fgets($fh);
		if ($line === FALSE)
			break;
		$linesplit = explode(" ", trim($line));
		$nameinfile = $linesplit[0];
	} while (strcmp($name, $nameinfile) !== 0);  // case sensitive
	echo "\nline: $line";
	fseek($fh,-1*strlen($line),SEEK_CUR);
	fwrite($fh,pad_string_trailing_space("xxxx 6.7.8.9 6789",20));*/
	
	/*do {
		$line = fgets($fh);
		if ($line === FALSE)
			break;
		$linesplit = explode(" ", trim($line));
		$nameinfile = $linesplit[0];
	} while (1);
	fclose($fh);*/
	/*
	if ($line !== FALSE) { // did not reach end of file, we found the name
		if ($address === NULL or $port === NULL) { // delete this line by moving all subsequent lines up one
			
		} else { // update this line
			
		}
	} else { // reached end of file, did not find the name
		if ($address === NULL or $port === NULL) { // want to delete name anyway
			return;
		} else { // add name at end of file as new entry
			
		}
		
	}
	fclose($fh);
	*/
}


// pass $address and $port NULL to delete
function put_CB_info($name, $address, $port) {
	global $dirname, $filename_CBinfo, $LINE_LENGTH;
	$fh = fopen($filename_CBinfo, 'c+') or die("Cannot open $filename_CBinfo");
	do {
		$line = fgets($fh);
		if ($line === FALSE)
			break;
		$linesplit = explode(" ", trim($line));
		$nameinfile = $linesplit[0];
	} while (strcmp($name, $nameinfile) !== 0); // case sensitive
	if ($line !== FALSE) { // did not reach end of file, we found the name
		if ($address === NULL or $port === NULL) { // delete this line by moving all subsequent lines up one
			while(TRUE) {
				$line = fgets($fh);
				if ($line === FALSE)
					break;
				fseek($fh,-2*$LINE_LENGTH-2,SEEK_CUR);
				fwrite($fh,$line."\n");
				fgets($fh);
			}
			ftruncate($fh,ftell($fh)-$LINE_LENGTH-1);
			echo "put_CB_info: Deleted $name\n";
		} else { // update this line
			fseek($fh,-1*strlen($line),SEEK_CUR);
			fwrite($fh,create_DB_line($name,$address,$port)."\n");
			echo "put_CB_info: Updated $name\n";
		}
	} else { // reached end of file, did not find the name
		if ($address === NULL or $port === NULL) { // want to delete name anyway
			// do nothing
			echo "put_CB_info: $name to delete does not exist\n";
		} else { // add name at end of file as new entry
			fseek($fh,0,SEEK_CUR);
			$str = create_DB_line($name,$address,$port)."\n";
			fwrite($fh,$str);
			echo "put_CB_info: Added $name\n";
		}
	}
	fclose($fh);
}
/* TESTING
put_CB_info("abcd","1.2.3.4","1234");
put_CB_info("defg","2.3.4.5","2345");
put_CB_info("pqrs","40.50.60.70","30000");
put_CB_info("wxyz","127.0.0.1","999");

put_CB_info("defg","99.999.99.999","6969");
put_CB_info("abcd",NULL,NULL);
put_CB_info("pqrs",NULL,NULL);
*/

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