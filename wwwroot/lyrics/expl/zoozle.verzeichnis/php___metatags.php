<?php
if (!defined('IN_OI6G92B86HTS'))
	die ('Acces denied');

function GetMetaTags()
{
	global $page_lang;
	global $display_page;
	global $max_metatag_characters;
	global $page_title;
	$meta = '<meta http-equiv="language"			content="'.$page_lang.'" />'."\r\n";
	switch ($display_page)
	{
		case PAGE_DIRECTORY:
			$category = GetDownloadCategory();
			$letter = $_GET['l'];
			if (!isset($letter) || $letter == '' || $category == false) {
				RedirectPageNotFound();
				break;
			}
			global $maxentries_directory;
			$display_cat = GetDisplayCategory($category);
			$letter = strtoupper($letter);
			$page = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
			if ($page < 1)
				$page = 1;
			$display_lang = GetDisplayLang($page_lang);
			$page_title = $display_cat . ' ' . L_DOWNLOAD . ' ' . L_DIRECTORY . ' ' . $letter . ' ' . $display_lang . ' ' . L_PAGE . ' ' . $page;
			/*
			$order_sql = isset($_GET['s']) && $_GET['s'] == 'name' ? 'name ASC' : 'entrydate DESC';
			$start = ($page-1)*$maxentries_directory;
			$query = "SELECT name, name_short FROM $category"."_$page_lang WHERE name LIKE '$letter%' GROUP BY name ORDER BY $order_sql LIMIT $start, 5";
			$result = mysql_query($query);
			$description = $letter . ' ' . $display_cat . ' ' . L_DOWNLOAD . ' ' . GetDisplayLang($page_lang);
			$keywords = '';
			$count = 0;
			while ($row = mysql_fetch_object($result))
			{
				$count++;
				if ($count <= 3) // add the first 3 names to the description
					$description .= ' ' . $row->name;
				else // and name 4 and 5 to the keywords
					$keywords .= AddCommas($row->name);
			}
			if ($count >= 4)
				$keywords .= $page_lang == 'de' ? AddCommas(L_DOWNLOAD_LANG_KEYWORD, true) : L_DOWNLOAD;
			else // not enough search results for keywords
				$keywords = AddCommas($description, true);
			$description = TransformToMetaTag($description);
			$description = TruncateWords($description, $max_metatag_characters);
			$keywords = TransformToMetaTag($keywords);
			$keywords = TruncateWords($keywords, $max_metatag_characters);
			*/
			$description = sprintf(L_DIRECTORY_DESCRIPTION, $display_cat, L_DOWNLOAD, L_DIRECTORY, $letter, $display_lang, L_PAGE, $page);
			$keywords = $display_cat . ' ' . L_DOWNLOAD . ' ' . L_DIRECTORY . ' ' . $letter . ' ' . $display_lang;
			$keywords = AddCommas($keywords) . L_PAGE . ' ' . $page;
			$meta .= "<title>$page_title</title>\r\n";
			$meta .= '<meta name="description"			content="'.$description.'" />'."\r\n";
			$meta .= '<meta name="keywords"				content="'.$keywords.'" />'."\r\n";
			if (isset($_GET['s']) && $_GET['s'] != 'date')
				$meta .= '<meta name="robots"				content="noindex, follow" />'."\r\n";
			else if (isset($_GET['s']) || (isset($_GET['p']) && $page == 1)) {
				global $subdomain;
				global $domain_ending;
				$page_canonical = $page == 1 ? '' : "&p=$page";
				// note tha order-parameter 'date' can't be set on page 2
				$meta .= '<link rel="canonical"				href="http://'.$subdomain.'.zoozle.'.$domain_ending.'/verzeichnis.php?l='.strtolower($letter).$page_canonical.'" />'."\r\n";
			}
			return $meta;
			
		case PAGE_SEARCH:
			$category = GetDownloadCategory(true);
			$keyword = $_GET['q'];
			if (!isset($keyword) || $keyword == '' || $category == false) {
				RedirectPageNotFound();
				break;
			}
			else if (ContainsInfringement($keyword)) {
				RedirectToFrontpage($category);
				break;
			}
			$display_cat = GetDisplayCategory($category);
			$page = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
			if ($page < 1)
				$page = 1;
			if ($category == DOMAIN_USENET) {
				$page_title = $keyword . ' ' . $display_cat . ' ' . L_DOWNLOAD_RESULTS;
				if ($page != 1)
					$page_title .= ' ' . L_PAGE . ' ' . $page;
				$description = $keyword . ' ' . $display_cat . ' ' . L_DOWNLOAD . ' ' . GetDisplayLang($page_lang);
				$keywords = $keyword . ', ';
				$keywords .= $page_lang == 'de' ? AddCommas(L_DOWNLOAD_LANG_KEYWORD, true) : L_DOWNLOAD;
				$meta .= "<title>$page_title</title>\r\n";
				$meta .= '<meta name="description"			content="'.$description.'" />'."\r\n";
				$meta .= '<meta name="keywords"				content="'.$keywords.'" />'."\r\n";
				$meta .= '<meta name="robots"				content="noindex, follow" />'."\r\n";
				return $meta;
			}
			
			global $maxresults_search;
			$page_title = $keyword . ' ' . $display_cat . ' ' . L_DOWNLOAD_RESULTS;
			if ($page != 1)
				$page_title .= ' ' . L_PAGE . ' ' . $page;
			$start = ($page-1)*$maxresults_search;
			//$query = "SELECT name, MATCH (name) AGAINST ('$keyword' IN BOOLEAN MODE) AS relevance FROM $category"."_$page_lang WHERE MATCH (name) AGAINST ('$keyword' IN BOOLEAN MODE) GROUP BY name ORDER BY relevance DESC LIMIT $start, 5";
			//$query = "SELECT name FROM $category"."_$page_lang WHERE MATCH (name) AGAINST ('$keyword') GROUP BY name LIMIT $start, 5";
			global $sql_rows;
			global $store_search;
			//$query = "SELECT name FROM names_$category"."_$page_lang WHERE MATCH (name) AGAINST ('$keyword') LIMIT $start, 5";
			$query = "SELECT name, name_short, entrydate, MATCH (name) AGAINST ('$keyword') AS relevance FROM names_$category"."_$page_lang WHERE MATCH (name) AGAINST ('$keyword') ORDER BY relevance DESC, entrydate DESC LIMIT $start, $maxresults_search";
			$result = mysql_query($query);
			$description = $keyword . ' ' . $display_cat . ' ' . L_DOWNLOAD . ' ' . GetDisplayLang($page_lang) . ' ' . L_WITH_RESULTS_LIKE . ' ';
			$keywords = '';
			$i = 0;
			while ($sql_rows[$i] = mysql_fetch_object($result))
			{
				if ($i < 3) // add the first 3 names to the description
					$description .= GetDescriptionSeparator($i) . $sql_rows[$i]->name;
				else// if ($i < 5)  // and name 4 and 5 to the keywords
					$keywords .= $sql_rows[$i]->name . ' ';
				$i++;
				if ($i == RESULT_TO_GET_RELEVANCE && (int)$sql_rows[$i-1]->relevance >= MIN_SEARCH_RELEVANCE)
					$store_search = true;
			}
			if ($i < 4) // not enough search results for keywords
				$keywords = $description;
			$keywords = GetUniqueKeywords($keywords);
			$keywords = AddCommas($keywords);
			$keywords .= $page_lang == 'de' ? AddCommas(L_DOWNLOAD_LANG_KEYWORD, true) : L_DOWNLOAD;
			$description = TransformToMetaTag($description);
			$description = TruncateWords($description, $max_metatag_characters);
			$keywords = TransformToMetaTag($keywords);
			$keywords = TruncateWords($keywords, $max_metatag_characters);
			$meta .= "<title>$page_title</title>\r\n";
			$meta .= '<meta name="description"			content="'.$description.'" />'."\r\n";
			$meta .= '<meta name="keywords"				content="'.$keywords.'" />'."\r\n";
			if ($i == 0)
				$meta .= '<meta name="robots"				content="noindex, follow" />'."\r\n";
			if (isset($_GET['p']) && $page == 1) {
				global $subdomain;
				global $domain_ending;
				$page_canonical = '';
				$meta .= '<link rel="canonical"				href="http://'.$subdomain.'.zoozle.'.$domain_ending.'/suche.php?q='.urlencode($keyword).$page_canonical.'" />'."\r\n";
			}
			return $meta;
			
		case PAGE_DOWNLOAD:
			$category = GetDownloadCategory();
			$name_short = $_GET['n'];
			if (!isset($name_short) || $name_short == '' || $category == false) {
				RedirectPageNotFound();
				break;
			}
			else if (ContainsInfringement($name_short)) {
				RedirectToFrontpage($category);
				break;
			}
			global $download_link_limit;
			$display_cat = GetDisplayCategory($category);
			//$query = "SELECT f.name_short, d.description FROM $category"."_$page_lang f LEFT OUTER JOIN descriptions_$page_lang d ON f.name_short = d.name_short WHERE f.name_short = '$name_short' LIMIT 1";
			$query = "SELECT name, name_short, link, entrydate FROM $category"."_$page_lang WHERE name_short = '$name_short' ORDER BY entrydate DESC, name ASC LIMIT $download_link_limit"; // name_short only for output here
			$result = mysql_query($query);
			global $rows_links;
			if ($rows_links[0] = mysql_fetch_object($result))
			{
				global $rows_descriptions;
				$i = 1;
				while ($rows_links[$i] = mysql_fetch_object($result))
					$i++;
				$query = "SELECT name, description FROM descriptions_$page_lang WHERE name_short = '$name_short'";
				$result = mysql_query($query);
				$i = 0;
				while ($rows_descriptions[$i] = mysql_fetch_object($result))
					$i++;
				
				$page_title = TransformToMetaTag($rows_links[0]->name_short) . ' ';
				if ($page_lang == 'de')
					$page_title .= $display_cat . ' ' . L_DOWNLOAD . ' ' . L_DOWNLOAD_LANG_KEYWORD;
				else
					$page_title .= L_DOWNLOAD . ' ' . $display_cat;
				
				$sources = '';
				for ($i = 0; $i < count($rows_links)-1; $i++)
				{
					$source = GetLinkSource($rows_links[$i]->link);
					if (strpos($sources, $source) === false)
						$sources .= ', ' . $source;
				}
				$sources = substr($sources, 2);
				
				global $related_downloads;
				$related_downloads = GetRelatedDownloads($category, $name_short);
				
				$description = '';
				if (isset($rows_descriptions[0]->description))
					$description = $rows_descriptions[0]->description;
				else {
					$related_addon = GetRelatedDownloadsForDescription($related_downloads, $rows_links[0]->name);
					$description = $rows_links[0]->name_short;
				}
				$description .= $related_addon . ' ' . L_DOWNLOAD_FOR_FREE . $sources . L_DOWNLOAD_DESCRIPTION_ADDON;
				$description = TransformToMetaTag($description);
				$description = TruncateWords($description, $max_metatag_characters);
				
				$lang_keyword = $page_lang == 'de' ? ', ' . AddCommas(L_DOWNLOAD_LANG_KEYWORD, true) : '';
				$related_addon = GetRelatedDownloadsForKeywords($related_downloads, $rows_links[0]->name);
				$keywords = AddCommas($rows_links[0]->name_short) . $display_cat . ', ' . L_DOWNLOAD . $related_addon . ', ' . $sources . $lang_keyword . L_DOWNLOAD_KEYWORDS_ADDON;
				$keywords = TransformToMetaTag($keywords);
				$keywords = TruncateWords($keywords, $max_metatag_characters);
				$meta .= "<title>$page_title</title>\r\n";
				$meta .= '<meta name="description"			content="'.$description.'" />'."\r\n";
				$meta .= '<meta name="keywords"				content="'.$keywords.'" />'."\r\n";
				$meta .= '<meta name="Revisit-After"			content="10 Days" />'."\r\n";
				return $meta;
			}
			//RedirectPageNotFound();
			RedirectToSearch();
			break;
			
		case PAGE_NEWS:
			$category = GetDownloadCategory();
			if ($category == false) {
				RedirectPageNotFound();
				break;
			}
			$display_cat = GetDisplayCategory($category);
			$display_lang = GetDisplayLang($page_lang);
			$page_title = sprintf(L_NEWS_TITLE, $display_cat);
			$description = sprintf(L_NEWS_DESCRIPTION, $display_cat, $display_lang);
			$keywords = sprintf(L_NEWS_KEYWORDS, $display_cat, $display_lang);
			$meta .= "<title>$page_title</title>\r\n";
			$meta .= '<meta name="description"			content="'.$description.'" />'."\r\n";
			$meta .= '<meta name="keywords"				content="'.$keywords.'" />'."\r\n";
			return $meta;
	}
	
	global $subdomain;
	$addon = '';
	switch ($subdomain)
	{
		case DOMAIN_RAPIDSHARE:
		case DOMAIN_TORRENT:
		case DOMAIN_EMULE:
			$addon = GetDisplayCategory($subdomain);
			$page_title = L_DEFAULT_TITLE_SHORT;
			break;
		default:
			$page_title = L_DEFAULT_TITLE;
	}
	$addon_title = $addon == '' ? '' : ' - ' . $addon;
	$addon_description = $addon == '' ? '' : ' ' . $addon;
	$addon_keyword = $addon == '' ? '' : ', ' . $addon;
	
	$page_title .= $addon_title;
	$meta .= "<title>$page_title</title>\r\n";
	$meta .= '<meta name="description"			content="'.L_META_DESCRIPTION.$addon_description.'" />'."\r\n";
	$meta .= '<meta name="keywords"				content="'.L_META_KEYWORDS.$addon_keyword.'" />'."\r\n";
	return $meta;
}
?>
