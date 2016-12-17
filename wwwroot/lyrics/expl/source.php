HTTP/1.1 200 OK
Date: Fri, 23 Jul 2010 17:42:18 GMT
Accept-Ranges: bytes
Connection: close
ETag: "12f-4bb20af2-7b6e01"
Last-Modified: Tue, 30 Mar 2010 14:30:10 GMT
Content-Type: text/plain
Content-Length: 303
Cache-Control: max-age=99990000, public
Cache-Control: max-age=1753032704
Expires: Tue, 09 Feb 2066 11:14:02 GMT

<?php
define('IN_OI6G92B86HTS', 'true');
include "php/config.php";
$display_page = PAGE_MAIN;
include "php/functions.php";
include "php/headers.php";
include 'php/displayfunctions.php';
include 'php/design.php';
include "php/metatags.php";

$design->PrintPage();
mysql_close($connection);
?>