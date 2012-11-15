<?php
error_reporting(E_ALL);
require 'file_info.php';

if ($dirname !== null and !is_dir($dirname))
  mkdir($dirname) or die("Cannot make directory".$dirname);
$filename_CBinfo = ($dirname === null ? "." : $dirname) . "/back_info.txt";
// use bool file_exists ( string $filename_CBinfo ) 
$pathinfo = pathinfo($filename_CBinfo);
echo "Dir name : $pathinfo[dirname]<br />\n";
$realpath = realpath($pathinfo[dirname]);
//echo "Real dir : "
echo $realpath;
//echo " <br />\n"
echo "<br />\nBase name: $pathinfo[basename] <br />\n";
echo "Extension: $pathinfo[extension] <br />\n";

$ds_address = $_GET["a"];
$ds_port = $_GET["p"];
if ($ds_address === null or $ds_port === null) {
  if (file_exists($filename_CBinfo))
    unlink($filename_CBinfo);
} else {
  echo "attmpting to write...<br/>\n";
  $fh = fopen($filename_CBinfo, 'w') or die("Cannot open $filename_CBinfo");
  fwrite($fh,$ds_address) or die("Cannot write");
  fwrite($fh,"\n");
  fwrite($fh,$ds_port);
  fclose($fh);
  echo "<b>done writing $filename_CBinfo </b><br/>\n";
  $filename_CBinfo = ($dirname === null ? "." : $dirname) . "/highest_id.txt";
  $fh = fopen($filename_CBinfo, 'w');
  fwrite($fh,'1');
  fclose($fh);
  echo "<b>done writing $filename_CBinfo </b><br/>\n";
}
?>