<?php
define('IN_OI6G92B86HTS', 'true');
include 'php/config.php';
$display_page = PAGE_NEWS;
$expires = NEWS_CACHE_MINUTES*60;
header('Pragma: public');
header('Cache-Control: max-age=' . $expires);
header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');
include 'php/functions.php';
include 'php/headers.php';
include 'php/displayfunctions.php';
include 'php/design.php';
include 'php/metatags.php';

//include 'php/news.class.php';

$design->PrintPage();
mysql_close($connection);
?>
