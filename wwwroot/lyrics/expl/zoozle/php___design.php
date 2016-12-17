<?php
if (!defined('IN_OI6G92B86HTS'))
	die ('Acces denied');

class Design
{
	var $template;
	
	function PrintPage()
	{
		global $display_page;
		global $subdomain;
		global $domain_ending;
		$this->LoadTemplate('general', 'html_head1');
		$this->template .= GetMetaTags();
		echo $this->template;
		$this->template = '';
		$this->LoadTemplate('general', 'html_head2');
		
		$on_load_action = ' onload="document.searchform.keyword.focus();"';
		switch ($display_page)
		{
			case PAGE_MAIN:
				$this->LoadTemplate('general', 'searchfield');
				global $page_lang;
				switch ($subdomain)
				{
					case DOMAIN_RAPIDSHARE:
					case DOMAIN_TORRENT:
					case DOMAIN_EMULE:
						$this->LoadTemplate('general', 'category');
						$title = GetDisplayCategory($subdomain) . ' ' . L_DOWNLOAD . ' ' . L_DIRECTORY . ' ' . GetDisplayLang($page_lang);
						$values = array('{domain_ending}' => $domain_ending,
										'{page_lang}' => $page_lang,
										'{title}' => $title,
										'{letters}' => GetDirectoryLetters(true),
										'{title_2}' => L_RECENT_DOWNLOADS,
										'{recent_downloads}' => GetRecentDownloads('', $recent_downloads_limit_home),
										'{title_3}' => L_RECENT_SEARCHES,
										'{recent_searches}' => GetRecentSearches(),
										'{friends}' => L_FRIENDS,
										'{emule_download}' => L_EMULE_DOWNLOAD,
										'{emule_mod_download}' => L_EMULE_MOD_DOWNLOAD,
										'{impressum}' => L_IMPRESSUM);
						$this->SetValues($values);
						break;
						
					default: // also for usenet (not linked, but just in case)
						$this->LoadTemplate('general', 'home');
						global $recent_downloads_limit_home;
						$values = array('{domain_ending}' => $domain_ending,
										'{page_lang}' => $page_lang,
										'{title}' => L_RECENT_DOWNLOADS,
										'{recent_downloads}' => GetRecentDownloads('', $recent_downloads_limit_home),
										'{title_2}' => L_RECENT_SEARCHES,
										'{recent_searches}' => GetRecentSearches(),
										'{friends}' => L_FRIENDS,
										'{emule_download}' => L_EMULE_DOWNLOAD,
										'{emule_mod_download}' => L_EMULE_MOD_DOWNLOAD,
										'{impressum}' => L_IMPRESSUM);
						$this->SetValues($values);
				}
				break;
				
			case PAGE_DIRECTORY:
				$this->LoadTemplate('general', 'searchfield');
				$letter = $_GET['l'];
				$category = GetDownloadCategory();
				/* Already checked in metatags.php
				if (!isset($letter) || $letter == '' || $category == false) {
					$this->LoadTemplate('directory', 'invalid');
					$values = array('{invalid_request}' => L_INVALID_REQUEST);
					$this->SetValues($values);
					break;
				}
				*/
				$this->LoadTemplate('directory', 'directory_body');
				$page = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
				if ($page < 1)
					$page = 1;
				$letter = strtoupper($letter);
				
				$directory = new zDirectory($letter, $category, $page);
				global $page_lang;
				$display_cat = GetDisplayCategory($category);
				$title = $display_cat . ' ' . L_DOWNLOADS . ' ' . $letter . ' ' . GetDisplayLang($page_lang) . ' ' . L_PAGE . ' ' . $page;
				$selected_dropdown = array('', '');
				if (isset($_GET['s']) && $_GET['s'] == 'name')
					$selected_dropdown[1] = ' selected="selected"';
				else
					$selected_dropdown[0] = ' selected="selected"';
				$values = array('{domain_ending}' => $domain_ending,
								//'{subdomain}' => $subdomain,
								'{page_lang}' => $page_lang,
								'{title}' => $title,
								'{letters}' => $directory->GetLetters(),
								//'{pages}' => $directory->GetPages(), // call this after GetEntries()
								'{other_categories}' => L_OTHER_CATEGORIES,
								'{other_categories_content}' => $directory->GetOtherCategories(),
								'{order_by}' => L_ORDER_BY,
								'{letter}' => strtolower($letter),
								'{order_date}' => L_ORDER_DATE,
								'{alphabet}' => L_ALPHABET,
								'{selected_date}' => $selected_dropdown[0],
								'{selected_alphabet}' => $selected_dropdown[1],
								'{entries}' => $directory->GetEntries(),
								'{pages}' => $directory->GetPages(),
								'{go_top}' => L_GO_TOP,
								'{recent_searches_text}' => L_RECENT_SEARCHES,
								'{recent_searches}' => GetRecentSearches(),
								'{friends}' => L_FRIENDS,
								'{emule_download}' => L_EMULE_DOWNLOAD,
								'{emule_mod_download}' => L_EMULE_MOD_DOWNLOAD);
				$this->SetValues($values);
				$on_load_action = '';
				break;
				
			case PAGE_SEARCH:
				$this->LoadTemplate('search', 'searchfield');
				$keyword = $_GET['q'];
				$category = GetDownloadCategory(true);
				/* Already checked in metatags.php
				if (!isset($keyword) || $keyword == '' || $category == false) {
					$this->LoadTemplate('search', 'invalid');
					$values = array('{invalid_search_request}' => L_INVALID_SEARCH_REQUEST);
					$this->SetValues($values);
					break;
				}
				*/
				$keyword = urldecode($keyword);
				$this->LoadTemplate('search', 'search_body');
				$this->LoadTemplate('search', 'search_bottom');
				
				$search = new Search($keyword, $category);
				$display_cat = GetDisplayCategory($category);
				$headline_search = sprintf(L_HEADLINE_SEARCH, $keyword, $display_cat);
				if ($category != DOMAIN_USENET)
					$values = array(//'{subdomain}' => $subdomain,
									'{category_download}' => sprintf(L_CATEGORY_DOWNLOAD, $display_cat),
									'{search_keyword}' => $keyword,
									'{headline_search}' => $headline_search,
									'{emule_download_title}' => L_EMULE_DOWNLOAD_TITLE,
									'{emule_download}' => L_EMULE_DOWNLOAD,
									'{emule_download_text}' => L_EMULE_DOWNLOAD_TEXT,
									'{100_mbits_downloads_link}' => L_100_MBITS_DOWNLOADS_LINK,
									'{keyword}' => $keyword,
									'{direct_downloads}' => L_DIRECT_DOWNLOADS,
									'[direct_downloads_text}' => L_DIRECT_DOWNLOADS_TEXT,
									'{100_mbits_downloads}' => L_100_MBITS_DOWNLOADS,
									'{100_mbits_download_content}' => $search->GetUsenetResults(), // do this before GetSearchResults()
									'{search_results}' => $search->GetSearchResults(),
									'{friends}' => L_FRIENDS,
									'{emule_download}' => L_EMULE_DOWNLOAD,
									'{emule_mod_download}' => L_EMULE_MOD_DOWNLOAD,
									'{recent_downloads_text}' => L_RECENT_DOWNLOADS,
									'{recent_downloads}' => GetRecentDownloads(),
									'{recent_searches_text}' => L_RECENT_SEARCHES,
									'{recent_searches}' => GetRecentSearches());
				else
					$values = array(//'{subdomain}' => $subdomain,
									'{category_download}' => sprintf(L_CATEGORY_DOWNLOAD, $display_cat),
									'{search_keyword}' => $keyword,
									'{headline_search}' => $headline_search,
									'{emule_download_title}' => L_EMULE_DOWNLOAD_TITLE,
									'{emule_download}' => L_EMULE_DOWNLOAD,
									'{emule_download_text}' => L_EMULE_DOWNLOAD_TEXT,
									'{100_mbits_downloads_link}' => L_100_MBITS_DOWNLOADS_LINK,
									'{keyword}' => $keyword,
									'{direct_downloads}' => L_DIRECT_DOWNLOADS,
									'[direct_downloads_text}' => L_DIRECT_DOWNLOADS_TEXT,
									'{100_mbits_downloads}' => '',
									'{100_mbits_download_content}' => '',
									'{search_results}' => $search->GetUsenetResults(),
									'{friends}' => L_FRIENDS,
									'{emule_download}' => L_EMULE_DOWNLOAD,
									'{emule_mod_download}' => L_EMULE_MOD_DOWNLOAD,
									'{recent_downloads_text}' => L_RECENT_DOWNLOADS,
									'{recent_downloads}' => GetRecentDownloads(),
									'{recent_searches_text}' => L_RECENT_SEARCHES,
									'{recent_searches}' => GetRecentSearches());
				$this->SetValues($values);
				$search->AddRecentSearch();
				$on_load_action = '';
				break;
				
			case PAGE_DOWNLOAD:
				$this->LoadTemplate('general', 'searchfield');
				$name_short = $_GET['n'];
				$category = GetDownloadCategory();
				/* Already checked in metatags.php
				if (!isset($name_short) || $name_short == '' || $category == false) {
					$this->LoadTemplate('download', 'invalid');
					$values = array('{invalid_download}' => L_INVALID_DOWNLOAD);
					$this->SetValues($values);
					break;
				}
				*/
				$name_short = urldecode($name_short);
				global $page_lang;
				$this->LoadTemplate('download', 'download_body');
				
				$download = new Download($name_short, $category);
				$title = $download->GetFirstTitle();
				$display_cat = GetDisplayCategory($category);
				$title_ad;
				if ($page_lang == 'de')
					$title_ad = $display_cat . ' ' . L_DOWNLOAD;
				else
					$title_ad = L_DOWNLOAD . ' ' . $display_cat;
				$title_h1 = "$title<br /><span class=\"lower_h1\">$title_ad</span>";
				$name_short_url = urlencode($name_short);
				$link_download_content = '<b><a target="_blank" href="http://'.$subdomain.'.zoozle.'.$domain_ending.'/download.php?n='.$name_short_url.'">'.$title.' '.$title_ad.'</a></b>';
				$headline_downloads = sprintf(L_HEADLINE_DOWNLOADS, GetDisplayCategory($category));
				$values = array('{title_h1}' => $title_h1,
								'{title}' => $title,
								'{lang}' => $page_lang,
								'{download}' => L_DOWNLOAD_BIG,
								'{name_short_url}' => $name_short_url,
								'{possibly_100_mbits}' => L_POSSIBLY_100_MBITS,
								'{go_bottom}' => L_GO_BOTTOM,
								'{link_us}' => L_LINK_US,
								'{link_us_content}' => L_LINK_US_CONTENT,
								'{link_download}' => L_LINK_DOWNLOAD,
								'{link_download_content}' => $link_download_content,
								'{description}' => $download->GetDescription(),
								'{related_downloads}' => GetRelatedDownloads($category, $name_short, true),
								'{download_client}' => $download->GetDownloadClient($display_cat),
								'{headline_downloads}' => $headline_downloads,
								'{headline_source}' => L_SOURCE,
								'{headline_date}' => L_DATE,
								'{links}' => $download->GetDownloadLinks(),
								'{search_other_categories}' => $download->GetOtherCategoriesSearch(),
								'{recent_downloads_text}' => L_RECENT_DOWNLOADS,
								'{recent_downloads}' => GetRecentDownloads($name_short),
								'{recent_searches_text}' => L_RECENT_SEARCHES,
								'{recent_searches}' => GetRecentSearches(),
								'{friends}' => L_FRIENDS,
								'{emule_download}' => L_EMULE_DOWNLOAD,
								'{emule_mod_download}' => L_EMULE_MOD_DOWNLOAD);
				$this->SetValues($values);
				$download->AddRecentDownload();
				$on_load_action = '';
				break;
				
			case PAGE_NEWS:
				$this->LoadTemplate('general', 'searchfield');
				$this->LoadTemplate('general', 'news');
				$category = GetDownloadCategory();
				$display_cat = GetDisplayCategory($category);
				global $page_lang;
				$values = array('{domain_ending}' => $domain_ending,
								'{title}' => sprintf(L_NEWS_TITLE, $display_cat),
								'{page_lang}' => $page_lang,
								'{news_data}' => GetNews($category));
				$this->SetValues($values);
				$on_load_action = '';
				break;
		}
		
		$checked_radio = array(' checked="checked"', '', '', '');
		switch ($subdomain)
		{
			case DOMAIN_RAPIDSHARE:
				$checked_radio = array(' checked="checked"', '', '', '');
				break;
			case DOMAIN_TORRENT:
				$checked_radio = array('', ' checked="checked"', '', '');
				break;
			case DOMAIN_EMULE:
				$checked_radio = array('', '', ' checked="checked"', '');
				break;
			case DOMAIN_USENET:
				$checked_radio = array('', '', '', ' checked="checked"');
				break;
		}
		
		$url = 'http://'.$subdomain.'.zoozle.' . $domain_ending . $_SERVER['REQUEST_URI'];
		global $page_title;
		$this->LoadTemplate('general', 'footer');
		$values = array('{subdomain}' => $subdomain,
						'{domain_ending}' => $domain_ending,
						'{home}' => L_HOME,
						'{rapidshare}' => L_RAPIDSHARE,
						'{torrent}' => L_TORRENT,
						'{emule}' => L_EMULE,
						'{news}' => L_NEWS,
						'{header_link_addon}' => L_HEADER_LINK_ADDON,
						'{url}' => urlencode($url),
						'{bookmark_title}' => urlencode($page_title),
						'{self_link_keywords_de}' => L_SELF_LINK_KEYWORDS_DE,
						'{self_link_keywords_en}' => L_SELF_LINK_KEYWORDS_EN,
						'{on_load_action}' => $on_load_action,
						'{search_keyword}'  => '',
						'{search}' => L_SEARCH,
						'{category}' => L_CATEGORY,
						'{usenet}' => L_USENET,
						'{checked_rapidshare}' => $checked_radio[0],
						'{checked_torrent}' => $checked_radio[1],
						'{checked_emule}' => $checked_radio[2],
						'{checked_usenet}' => $checked_radio[3],
						'{download_message}' => L_ADBLOCK_MESSAGE);
		$this->SetValues($values);
		echo $this->template;
		unset($this->template);
	}
	
	function LoadTemplate($file, $block)
	{
		$template = file_get_contents("./tpl/$file.tpl.php");
		$template = explode("<!-- $block -->\r\n", $template, 2);
		$template = explode("<!-- /$block -->", $template[1], 2);
		$this->template .= $template[0];
	}
	
	function SetValues($values)
	{
		$search;
		$replace;
		while (list($search, $replace) = each($values))
			$this->template = str_replace($search, $replace, $this->template);
	}
	
	public static function GetTemplate($file, $block)
	{
		$template = file_get_contents("./tpl/$file.tpl.php");
		$template = explode("<!-- $block -->\r\n", $template, 2);
		$template = explode("<!-- /$block -->", $template[1], 2);
		return $template[0];
	}
	
	public static function InsertValues($template, $values)
	{
		$search;
		$replace;
		while (list($search, $replace) = each($values))
			$template = str_replace($search, $replace, $template);
		return $template;
	}
}
$design = new Design();
?>
