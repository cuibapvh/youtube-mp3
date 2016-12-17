<?php
if (!defined('IN_OI6G92B86HTS'))
	die ('Acces denied');

function GetDownloadCategory($usenet = false)
{
	global $subdomain;
	switch ($subdomain)
	{
		case DOMAIN_RAPIDSHARE:
		case DOMAIN_TORRENT:
		case DOMAIN_EMULE:
			return $subdomain;
	}
	if ($usenet && $subdomain == DOMAIN_USENET)
		return $subdomain;
	return false;
}

function PerformUsenetSearch($keyword, $max_results)
{
	if ($keyword == '')
		return array();
	
	// check if a result is available in our database
	global $stop_refresh_days;
	$stop_refresh = date('Y-m-d', time() - $stop_refresh_days*24*60*60);
	$now = date('Y-m-d H:i:s');
	$query = "SELECT quantity, files FROM usenet_ads WHERE keyword = '$keyword' AND (entrydate <= '$stop_refresh' OR next_refresh >= '$now') LIMIT 1";
	$result = mysql_query($query);
	$row;
	if (($row = mysql_fetch_object($result)) != false && $row->quantity >= $max_results) {
		if ($row->quantity == $max_results)
			return unserialize($row->files);
		return GetArrayElements(unserialize($row->files), $max_results);
	}
	
	// send request to usenet
	$ctx = stream_context_create(array('http' => array('timeout' => 2)));
	$keyword_url = urlencode($keyword);
	//$results = @file_get_contents("http://search.usenext.de/search/searchfilegroup?search=$keyword_url&num=$max_results", 0, $ctx);
	$results = @file_get_contents("http://search.usenext.de/search/searchfilegroup_names?search=$keyword_url&num=$max_results", 0, $ctx);
	if ($results == false) {
		if ($row) {
			if ($row->quantity <= $max_results) // we have to accept less results, too
				return unserialize($row->files);
			return GetArrayElements(unserialize($row->files), $max_results);
		}
		else { // repeat query and ignore date
			$query = "SELECT quantity, files FROM usenet_ads WHERE keyword = '$keyword' LIMIT 1";
			$result = mysql_query($query);
			if (($row = mysql_fetch_object($result)) != false && $row->quantity >= $max_results) {
				if ($row->quantity == $max_results)
					return unserialize($row->files);
				return GetArrayElements(unserialize($row->files), $max_results);
			}
			return array();
		}
	}
	$results = explode("\r\n", $results);
	$result = array();
	$i;
	for ($i = 0; $i < count($results)-1; $i++)
	{
		//$file = explode(',', $results[$i]);
		//$result[$i] = utf8_encode($file[1]);
		$result[$i] = utf8_encode($results[$i]);
	}
	if ($row != false && $i < $max_results && $i < $row->quantity) { // less entries on new answer?
		if ($row->quantity == $max_results)
			return unserialize($row->files);
		return GetArrayElements(unserialize($row->files), $max_results);
	}
	
	// store result in our database
	global $usenet_refresh_days;
	$now = date('Y-m-d');
	$next_refresh = date('Y-m-d H:i:s', time() + $usenet_refresh_days*24*60*60);
	$files = serialize($result);
	$files = addcslashes($files, "'");
	$query = "SELECT COUNT(*) FROM usenet_ads WHERE keyword = '$keyword'";
	$check_result = mysql_query($query);
	$row = mysql_fetch_row($check_result);
	if ($row[0] != 0)
		$query = "UPDATE usenet_ads SET next_refresh = '$next_refresh', quantity = '$i', files = '$files' WHERE keyword = '$keyword' LIMIT 1";
	else
		$query = "INSERT DELAYED IGNORE INTO usenet_ads (keyword, entrydate, next_refresh, quantity, files) VALUES ('$keyword', '$now', '$next_refresh', '$i', '$files')";
	mysql_query($query);
	
	if ($i > $max_results) // usenet seems to send more results sometimes
		return GetArrayElements($result, $max_results);
	return $result;
}

function GetArrayElements($array, $max)
{
	$result = array();
	for ($i = 0; $i < $max; $i++)
		$result[$i] = $array[$i];
	return $result;
}

function PerformLowerCaseRedirect($parameter, $filter_chars)
{
	$parameter = urldecode($parameter);
	$lower = strtolower($parameter);
	if ($filter_chars)
		$lower = FilterBadCharsForUrl($lower);
	if ($lower != $parameter)
	{
		global $subdomain;
		global $domain_ending;
		$uri = strtolower(urldecode($_SERVER['REQUEST_URI']));
		if ($filter_chars)
			$uri = FilterBadCharsForUrl($uri);
		$uri = str_replace(' ', '+', trim($uri)); // simple urlencode
		header('HTTP/1.1 301 Moved Permanently');
		header("Location: http://$subdomain.zoozle.$domain_ending$uri");
		//header('Connection: close');
		die();
	}
}

function FilterBadCharsForUrl($string, $full_url = true)
{
	$string = preg_replace("/[,_-]/", " ", $string); // replace spaces // don't replace . here
	$string = str_replace("%20", " ", $string); // replace html spaces
	if ($full_url) // don't replace ? = / & here
		$string = preg_replace("/[^a-z0-9 ÄäÖöÜüßÁáÀàÂâÉéÈèÊêÍíÌìÎîÓóÒòÔôÚúÙùÛûÇçæ?\/.=&]+/i", " ", $string);  // remove special chars
	else
		$string = preg_replace("/[^a-z0-9 ÄäÖöÜüßÁáÀàÂâÉéÈèÊêÍíÌìÎîÓóÒòÔôÚúÙùÛûÇçæ]+/i", " ", $string);
	$string = preg_replace("/[ ]+/", " ", $string); // remove double-spaces
	$string = preg_replace("/=[ ]+/", "=", $string); // remove space after GET-parameter
	$string = trim($string);
	return $string;
}

function Date_MysqlToDisplay($date)
{
	$year; $month; $day;
	global $page_lang;
	$date = substr($date, 0, 10);
	list($year, $month, $day) = explode('-', $date);
	if ($page_lang == 'de')
		return sprintf("%02d.%02d.%04d", $day, $month, $year);
	else if ($page_lang == 'en')
		return sprintf("%02d/%02d/%04d", $month, $day, $year);
	return '';
}

function TransformToMetaTag($string)
{
	$string = str_replace('<br />', '', $string);
	$string = str_replace('"', '', $string);
	$string = str_replace("\t", " ", $string);
	$string = str_replace("\r", "", $string);
	$string = str_replace("\n", " ", $string);
	$string = preg_replace("/[ ]+/", " ", $string); // remove double-spaces
	//$string = htmlspecialchars($string);
	return $string;
}

function TruncateWords($text, $length)
{
	$length = abs((int)$length);
	if (strlen($text) > $length)
		$text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
	return $text;
}

function RedirectPageNotFound()
{
	global $connection;
	if (mysql_ping($connection) == false)
		return;
	global $subdomain;
	global $domain_ending;
	header('HTTP/1.1 404 Not Found');
	//header("Location: http://$subdomain.zoozle.$domain_ending/");
	//header('Connection: close');
	$template = Design::GetTemplate('general', 'html_head1');
	$template .= Design::GetTemplate('general', 'page_not_found');
	$template .= Design::GetTemplate('general', 'footer');
	global $page_lang;
	$values = array('{page_lang}' => $page_lang,
					'{page_title}' => L_PAGE_NOT_FOUND,
					'{back_to_homepage}' => L_BACK_TO_HOMEPAGE,
					'{subdomain}' => $subdomain,
					'{domain_ending}' => $domain_ending);
	$template = Design::InsertValues($template, $values);
	echo $template;
	unset($template);
	mysql_close($connection);
	die();
}

function AddCommas($string, $no_end_comma = false)
{
	//$string = str_replace(' ', ', ', $string); // faster, but no filter for short words
	global $min_keyword_length;
	$string_arr = explode(' ', $string);
	$string = '';
	foreach ($string_arr as $cur_string)
	{
		if (strlen($cur_string) < $min_keyword_length)
			continue;
		$string .= ', ' . $cur_string;
	}
	$string = substr($string, 2);
	if (!$no_end_comma)
		$string .= ', ';
	return $string;
}

function GetUniqueKeywords($keywords)
{
	$keyword_arr = explode(' ', $keywords);
	$keywords = '';
	foreach ($keyword_arr as $keyword)
	{
		if (stripos($keywords, $keyword) === false)
			$keywords .= ' ' . $keyword;
	}
	$keywords = substr($keywords, 1);
	return $keywords;
}

function GetDescriptionSeparator($i)
{
	switch ($i)
	{
		case 0:		return '';
		//case 1:		return ', ';
		case 2:		return ' ' . L_AND . ' ';
		default:	return ', ';
	}
}

function EntriesExist($letter)
{
	global $subdomain;
	global $page_lang;
	$query = "SELECT COUNT(*) FROM names_$subdomain"."_$page_lang WHERE name LIKE '$letter%'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	if ($row[0] != 0)
		return true;
	return false;
}

function DeleteLetterFromCache($letter)
{
	global $subdomain;
	global $page_lang;
	$query = "DELETE FROM letters_$page_lang WHERE category = '$subdomain' AND letter = '$letter' LIMIT 1";
	mysql_query($query);
}

function UpdateCachedLetter($letter, $content)
{
	global $subdomain;
	global $page_lang;
	
	$query = "SELECT COUNT(*) FROM letters_$page_lang WHERE category = '$subdomain' AND letter = '$letter'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	$content = addcslashes($content, "'");
	if ($row[0] != 0)
		$query = "UPDATE letters_$page_lang SET letters = '$content' WHERE category = '$subdomain' AND letter = '$letter' LIMIT 1";
	else
		$query = "INSERT DELAYED IGNORE INTO letters_$page_lang (category, letter, letters) VALUES ('$subdomain', '$letter', '$content')";
	mysql_query($query);
}

function GetLinkSource($link)
{
	$source = parse_url($link);
	$source = $source['host'];
	if (substr($source, 0, 4) == 'www.')
		return substr($source, 4);
	return $source;
}

function GetRelatedDownloads($category, $name_short, $html_result = false)
{
	global $page_lang;
	global $related_downloads;
	$already_stored = true;
	if ($related_downloads == '')
	{
		// check if cached result available
		$now = date('Y-m-d H:i:s');
		$query = "SELECT related FROM files_$category"."_$page_lang WHERE name_short = '$name_short' AND next_refresh >= '$now' LIMIT 1";
		$result = mysql_query($query);
		if ($row = mysql_fetch_object($result))
			$related_downloads = unserialize($row->related);
	}
	
	if ($related_downloads == '')
	{
		// generate result
		$already_stored = false;
		global $search_category;
		global $max_related_downloads;
		$related_downloads = array();
		foreach ($search_category as $index => $cat)
		{
			if ($index == $category)
				continue;
			if ($index == DOMAIN_USENET)
				$related_downloads[$index] = PerformUsenetSearch($name_short, $max_related_downloads);
			else
			{
				$query = "SELECT name, name_short, MATCH(name) AGAINST('$name_short') AS relevance FROM names_$index"."_$page_lang  WHERE MATCH(name) AGAINST('$name_short') ORDER BY relevance DESC LIMIT $max_related_downloads";
				$result = mysql_query($query);
				$i = 0;
				while ($cur_row = mysql_fetch_object($result))
				{
					$related_downloads[$index][$i] = $cur_row;
					$i++;
				}
			}
		}
	}
	
	if ($already_stored == false)
	{
		// store result into database (empty results will get stored here, too)
		global $related_refresh_days;
		$next_refresh = date('Y-m-d H:i:s', time() + $related_refresh_days*24*60*60);
		$related = serialize($related_downloads);
		$related = addcslashes($related, "'");
		$query = "INSERT DELAYED IGNORE INTO files_$category"."_$page_lang (name_short, next_refresh, related) VALUES ('$name_short', '$next_refresh', '$related')";
		mysql_query($query);
	}
	
	if ($html_result == false)
		return $related_downloads;
		
	// generate html result
	global $search_category;
	global $max_related_downloads;
	global $domain_ending;
	$content = '';
	foreach ($search_category as $index => $cat)
	{
		if ($index == $category)
			continue;
		$client = '';
		$client_newline = '';
		if ($index == DOMAIN_USENET || $index == DOMAIN_EMULE) {
			$client_name = sprintf(L_CLIENT_DOWNLOAD, $cat);
			$ignore_adblock = $index == DOMAIN_EMULE ? 'true' : 'null';
			$client = "<div class=\"client_download\"><a class=\"green_link\" href=\"javascript:;\" rel=\"nofollow\" title=\"$client_name\" onClick=\"downloadFile('$index"."_client', $ignore_adblock, '$name_short'); return false;\"><span class=\"small\">$client_name</span></a></div><br />";
			$client_newline = '<br />' . $client;
		}
		else if ($index == DOMAIN_RAPIDSHARE) {
			global $rapidshare_client_page;
			$client_name = sprintf(L_CLIENT_DOWNLOAD, $cat);
			$client = "<div class=\"client_download\"><a class=\"green_link\" href=\"$rapidshare_client_page\" title=\"$client_name\" target=\"_blank\"><span class=\"small\">$client_name</span></a></div><br />";
			$client_newline = '<br />' . $client;
		}
		$content .= "<br /><br /><b>$cat:</b><br />
			";
		$cur_conntent = '';
		if ($index == DOMAIN_USENET)
		{
			$results = false;
			if (isset($related_downloads[$index]))
				foreach ($related_downloads[$index] as $filename)
				{
					$results = true;
					$cur_conntent .= "<li>$filename</li>";
					$i++;
				}
			if ($results)
				$content .= '<a href="javascript:;" rel="nofollow\" title="'.L_DOWNLOAD.'" onClick="downloadFile(\'download_related\', null, \''.$name_short.'\'); return false;" target="_blank">' . $cur_conntent . '</a>' . $client;
			else
				$content .= L_NO_RESULTS . $client_newline;
		}
		else
		{
			$results = false;
			if (isset($related_downloads[$index]))
				foreach ($related_downloads[$index] as $row)
				{
					$results = true;
					$name_short = urlencode(strtolower($row->name_short));
					$cur_conntent .= "<li><a href=\"http://$index.zoozle.$domain_ending/download.php?n=$name_short\" title=\"$row->name\">$row->name</a></li>";
					$i++;
				}
			if ($results)
				$content .= $cur_conntent . $client;
			else
				$content .= L_NO_RESULTS . $client_newline;
		}
	}
	$content = L_RELATED_DOWNLOADS . '<br /><br />' . substr($content, 12);
		
	return $content;
}

function GetRelatedDownloadsForDescription($related_downloads, $name)
{
	global $search_category;
	$related_addon = '';
	foreach ($search_category as $index => $cat)
	{
		if ($index == DOMAIN_USENET || !isset($related_downloads[$index]))
			continue;
		foreach ($related_downloads[$index] as $row)
		{
			if ($name == $row->name || strpos($related_addon, $row->name) !== false)
				continue;
			$related_addon .= ' ' . $row->name;
		}
	}
	return $related_addon;
}

function GetRelatedDownloadsForKeywords($related_downloads, $name)
{
	global $search_category;
	global $min_keyword_length;
	$related_addon = '';
	foreach ($search_category as $index => $cat)
	{
		if ($index == DOMAIN_USENET || !isset($related_downloads[$index]))
			continue;
		foreach ($related_downloads[$index] as $row)
		{
			if ($name == $row->name)
				continue;
			$string_arr = explode(' ', $row->name);
			foreach ($string_arr as $cur_string)
			{
				if (strlen($cur_string) < $min_keyword_length || strpos($related_addon, $cur_string) !== false)
					continue;
				$related_addon .= ', ' . $cur_string;
			}
		}
	}
	return $related_addon;
}

function ContainsInfringement($keyword)
{
	$query = "SELECT keyword FROM infringements";
	$result = mysql_query($query);
	while ($row = mysql_fetch_object($result))
	{
		if (stripos($keyword, $row->keyword) !== false)
			return true;
	}
	/*
	// faster solution
	$query = "SELECT COUNT(*) FROM infringements WHERE keyword LIKE '%$keyword%'";
	$result = mysql_query($query);
	$row = mysql_fetch_row($result);
	if ($row[0] != 0)
		return true;
	*/
	return false;
}

function RedirectToFrontpage($category)
{
	global $connection;
	mysql_close($connection);
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://$category.zoozle.net/");
	die();
}

function RedirectToSearch()
{
	global $connection;
	if (mysql_ping($connection) == false)
		return;
	global $subdomain;
	global $domain_ending;
	mysql_close($connection);
	$name_short = urlencode(urldecode($_GET['n'])); // to be sure
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://$subdomain.zoozle.$domain_ending/suche.php?q=$name_short");
	die();
}
?>
