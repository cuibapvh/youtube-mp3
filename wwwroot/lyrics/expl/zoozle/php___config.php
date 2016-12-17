<?php
if (!defined('IN_OI6G92B86HTS'))
	die ('Acces denied');

// Database Configuration
//$host = '192.168.39.6';
$host = 'localhost';
$username = 'zoozle2_net';
$password = 'hDf893bdsfb';
$database = 'zoozle_v2';


// Page Configuration
define('PAGE_MAIN', 0);
define('PAGE_DIRECTORY', 1);
define('PAGE_SEARCH', 2);
define('PAGE_DOWNLOAD', 3);
define('PAGE_NEWS', 4);

define('DOMAIN_MAIN', 'www');
define('DOMAIN_RAPIDSHARE', 'rapidshare');
define('DOMAIN_TORRENT', 'torrent');
define('DOMAIN_EMULE', 'emule');
define('DOMAIN_USENET', 'usenet');


// General Configuration
$max_metatag_characters = 290;
$min_keyword_length = 3;
$rapidshare_client_page = 'http://www.rsdownloader.org/';


// Download Page Configuration
// order for related downloads
$search_category = array(DOMAIN_USENET => 'Usenet',
						 DOMAIN_RAPIDSHARE => 'RapidShare',
						 DOMAIN_TORRENT => 'Torrent',
						 DOMAIN_EMULE => 'eMule');
$max_related_downloads = 5;
$related_refresh_days = 3;
$download_link_limit = 3;
$recent_downloads_limit = 3; // also on search page
$recent_searches_limit = 3; // also on search page


// Search Page Configuration
$max_keyword_cache_len = 64; // varchar(64) in SQL
$search_engines = 'google|msn|Slurp|Ask|yahoo|altavista|search|bing';
$usenet_results_search = 20;
$usenet_results_pages = 5;
$maxresults_search = 20;
$min_keyword_len = 2;
$search_cache_hours = 5;
$maxpages_usenet = 3;
define('MIN_SEARCH_RELEVANCE', 15);
define('RESULT_TO_GET_RELEVANCE', 5);
define('GROUP_SAME_RESULTS', 3);
/*
$search_hints['de'] = array('http://www.gigaflat.com/affiliate/t/474',
							'FriendlyDuck',
							'http://www.opensubtitles.org/search2/sublanguageid-auto/moviename-%s');
$search_hints['en'] = array('http://nowdownloadall.com/index.asp?PID=0bccfa14-cc72-4543-a7bc-23d88e9301d0&q=%s',
							'http://click.yottacash.com/?PID=1bbca34d-2bbb-4f2b-835f-7409fe81d5bf',
							'http://www.opensubtitles.org/search2/sublanguageid-auto/moviename-%s');
*/


// Directory Configuration
$maxentries_directory = 100;
$skip_pages_directory = 30;

// News Configuration
define('NEWS_ENTRY_LIMIT', 200);
define('NEWS_CACHE_MINUTES', 5);


// Main Page Configuration
$recent_downloads_limit_home = 5;


// Usenet Configuration
$stop_refresh_days = 720;
$usenet_refresh_days = 7;
?>
