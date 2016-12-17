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
