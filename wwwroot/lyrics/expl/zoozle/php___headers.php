<?php
if (!defined('IN_OI6G92B86HTS'))
	die ('Acces denied');

// Redirect old Searches
if (isset($_GET['suche']))
{
	$keyword = urldecode($_GET['suche']);
	$keyword = urlencode(FilterBadCharsForUrl($keyword, false));
	$category = $_GET['s'];
	if ($category == 'rapidshare' || $category == 'torrent' || $category == 'emule' || $category == 'usenet') {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: http://$category.zoozle.net/suche.php?q=$keyword");
		die();
	}
}

$subdomain = DOMAIN_MAIN;
$domain_parts = explode('.', $_SERVER['HTTP_HOST'], 3);
switch ($domain_parts[0])
{
	case DOMAIN_RAPIDSHARE:
	case DOMAIN_TORRENT:
	case DOMAIN_EMULE:
	case DOMAIN_USENET:
		if (count($_GET) == 0)
			header('Cache-Control: no-cache');
		$subdomain = $domain_parts[0];
		break;
	case DOMAIN_MAIN:
		header('Cache-Control: no-cache');
		break;
	default:
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: http://www.".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		die();
}

//$domain_ending = $domain_parts[2]; // seems to be buggy
$domain_ending = 'net';
$page_lang = $domain_ending == 'net' ? 'de' : 'en';
//$domain_ending = $page_lang == 'de' ? 'net' : 'org';
include("lang/$page_lang.php");
// set in metatags.php
$page_title;
$sql_rows;
$store_search = false;
$rows_links;
$rows_descriptions;
$related_downloads = '';

// Redirect to lowercase URL
if (isset($_GET['n']))
	PerformLowerCaseRedirect($_GET['n'], true);
if (isset($_GET['c']))
	PerformLowerCaseRedirect($_GET['c'], false);
if (isset($_GET['q']))
	PerformLowerCaseRedirect($_GET['q'], true);
if (isset($_GET['l']))
	PerformLowerCaseRedirect($_GET['l'], false);
if (isset($_GET['s']))
	PerformLowerCaseRedirect($_GET['s'], false);

$connection = mysql_connect($host, $username, $password);
mysql_select_db($database);
mysql_query("SET NAMES 'utf8'");
mysql_query("SET CHARACTER SET 'utf8'");

// do we want to send last modified header?
?>
