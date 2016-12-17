<?php
if (!defined('IN_OI6G92B86HTS'))
	die ('Acces denied');

function GetDisplayCategory($category)
{
	switch($category)
	{
		case DOMAIN_RAPIDSHARE:
			return L_RAPIDSHARE;
		case DOMAIN_TORRENT:
			return L_TORRENT;
		case DOMAIN_EMULE:
			return L_EMULE;
		case DOMAIN_USENET:
			return L_USENET;
	}
	return '';
}

function GetDisplayLang($language)
{
	switch ($language)
	{
		case 'de':
			return L_GERMAN;
		case 'en':
			return L_ENGLISH;
	}
	return '';
}

function GetRecentDownloads($exclude_name_short = '', $limit = 0)
{
	global $page_lang;
	global $domain_ending;
	$content = '';
	if ($limit == 0) {
		global $recent_downloads_limit;
		$limit = $recent_downloads_limit;
	}
	$query = "SELECT category, name_short FROM latest_files_$page_lang ";
	if ($exclude_name_short != '')
		$query .= "WHERE name_short != '$exclude_name_short' ";
	$query .= "ORDER BY time DESC LIMIT $limit";
	$result = mysql_query($query);
	while ($row = mysql_fetch_object($result))
	{
		$name_short_url = urlencode($row->name_short);
		$display_cat = GetDisplayCategory($row->category) . ' ' . L_DOWNLOAD;
		$content .= "<a href=\"http://$row->category.zoozle.$domain_ending/download.php?n=$name_short_url\" title=\"$row->name_short $display_cat\">$row->name_short</a><br />";
	}
	return $content;
}

function GetRecentSearches($exclude_keyword = '')
{
	global $page_lang;
	global $recent_searches_limit;
	global $domain_ending;
	$query = "SELECT category, keyword FROM latest_searches_$page_lang ";
	if ($exclude_keyword != '')
		$query .= "WHERE keyword != '$exclude_keyword' ";
	$query .= "ORDER BY time DESC LIMIT $recent_searches_limit";
	$result = mysql_query($query);
	$content = '';
	while ($row = mysql_fetch_object($result))
	{
		$keyword_url = urlencode($row->keyword);
		$display_cat = GetDisplayCategory($row->category) . ' ' . L_DOWNLOAD;
		$content .= "<a href=\"http://$row->category.zoozle.$domain_ending/suche.php?q=$keyword_url\" title=\"$row->keyword $display_cat\">$row->keyword</a><br />";
	}
	return $content;
}

function GetDirectoryLetters($cached)
{
	global $subdomain;
	global $page_lang;
	if ($cached)
		return file_get_contents("./cache/letters_$subdomain"."_$page_lang.html");
	
	global $domain_ending;
	$display_cat = GetDisplayCategory($subdomain);
	$numbers = '0123456789';
	$alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$alphabet_lower = 'abcdefghijklmnopqrstuvwxyz'; // faster than strtolower
	$content = '';
	for ($i = 0; $i < strlen($numbers); $i++)
	{
		if (EntriesExist($numbers[$i]))
			$content .= " <a href=\"http://$subdomain.zoozle.$domain_ending/verzeichnis.php?l=$numbers[$i]\" title=\"$numbers[$i] $display_cat ".L_DOWNLOADS."\">$numbers[$i]</a>";
		else
			$content .= " &nbsp;";
	}
	$content .= " <br />\r\n";
	for ($i = 0; $i < strlen($alphabet); $i++)
	{
		$first_lower = $alphabet_lower[$i];
		$first_upper = $alphabet[$i];
		for ($u = 0; $u < strlen($alphabet); $u++)
		{
			if (EntriesExist($first_upper.$alphabet[$u]))
				$content .= " <a href=\"http://$subdomain.zoozle.$domain_ending/verzeichnis.php?l=$first_lower$alphabet_lower[$u]\" title=\"$first_upper$alphabet[$u] $display_cat ".L_DOWNLOADS."\">$first_upper$alphabet[$u]</a>";
			else
				$content .= " <span class=\"white\">__</span>";
		}
		$content .= " <br />\r\n";
	}
	$content = substr($content, 1);
	return $content;
}

function GetNews($category)
{
	global $page_lang;
	global $domain_ending;
	$query = "SELECT name, name_short FROM names_$category"."_$page_lang ORDER BY entrydate DESC LIMIT " . NEWS_ENTRY_LIMIT;
	$result = mysql_query($query);
	$content = '';
	while ($row = mysql_fetch_object($result))
	{
		$link = urlencode(strtolower($row->name_short));
		$content .= " <a href=\"http://$category.zoozle.$domain_ending/download.php?n=$link\" title=\"$row->name\">$row->name</a><br />\r\n";
	}
	return $content;
}
?>
