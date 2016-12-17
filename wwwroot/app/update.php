<?php
require_once( "../lib/functions.inc.php");
$function 	= new Functions();
$version = $function->secureString($_REQUEST['v']);
echo "0";
exit(0);
?>