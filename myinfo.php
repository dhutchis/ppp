<?php
error_reporting(E_ALL);

echo "_REQUEST: (length = ".count($_REQUEST).")<ul>\n";
foreach($_REQUEST as $k => $v)
 echo "\t<li>".$k."\t->\t".$v."</li>\n";
$headers = apache_request_headers();
echo "</ul>";
echo "<br/>HEADERS: (length = ".count($headers).")<ul>\n";
foreach ($headers as $k => $v)
 echo "\t<li>".$k."\t->\t".$v."</li>\n";
echo "</ul><br/>_SERVER: (length = ".count($_SERVER).")<ul>\n";
foreach($_SERVER as $k => $v)
 echo "\t<li>".$k."\t->\t".$v."</li>\n";
echo "</ul>";
echo phpinfo();

?>