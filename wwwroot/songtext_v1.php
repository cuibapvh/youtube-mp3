<?php

/*
error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
require_once( "lib/config.inc.php" );
require_once( "lib/template.inc.php" );
require_once( "lib/functions.inc.php");
require_once( "lib/search.inc.php");
require_once( "lib/logging.inc.php");

$search 			= new Search();
$function 			= new Functions();
$design 			= new Template();
$config 			= new Config();
$log 				= new Logging();

$searchQuery 		= ucfirst( urldecode($function->secureString($_REQUEST['lyrics'])));
$title_tag 			= substr($searchQuery, 0, 50) . " Youtube MP3";

$mein_text 			= "";
$searchTitle 		= trim(preg_replace("/\([^)]+\)/","", $searchQuery));
$searchTitle 		= preg_replace('/\W/',' ', $searchTitle);
$searchTitle		= $function->secureString($searchTitle); 
$maxResults			= $config->lyrics_search_count();
$SongtextRawContent = $search->SphinxSongtextSearch( $searchTitle, $maxResults );
$cache_uri 			= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$count				= count($SongtextRawContent);

if ( $count <= 1 || !is_numeric($count) ){
	$robots = "NOINDEX,FOLLOW";
} elseif ( $count > 1 && is_numeric($count)) {
	$robots = "INDEX,FOLLOW,ALL";
};

$mein_text .= <<<END
	<ul itemscope itemtype="http://schema.org/MusicRecording" class="haudio">
END;

for ( $ArrayCount=0; $ArrayCount<=count($SongtextRawContent) - 1; $ArrayCount++ ) {
	list( $songtext_title,$songtext_artist,$songtext_content ) = explode('#####', $SongtextRawContent[$ArrayCount] );
	$songtext_content 	= preg_replace('#(<br\ ?\/?>)+#i', "<br />", $songtext_content); # 
	$songtext_content 	= preg_replace('#(<br */?>\s*)+#i', "<br />", $songtext_content); # 
	$lang				= $function->GetLanguageFromString($songtext_content);
	$duration			= $function->getDuration($songtext_title);
	$songtext_content	= $function->clearUTF($songtext_content);
	$songtext_content	= str_ireplace($searchQuery, "<b><strong><i>" . $searchQuery . "</i></strong></b>", $songtext_content);
	$songtext_content  	= $function->autolink($songtext_content, array("target"=>"_blank","rel"=>"nofollow"));	
	$searchQueryString	= strtolower(str_replace(" ","+",trim($searchQuery)));
	$searchsongtext_title	= strtolower(str_replace(" ","+",trim($songtext_title)));
	
	if ( $ArrayCount == 0 ){
		$mein_text .= <<<END
		<a name="top"></a>
		<li>
			<h2><span itemprop="byArtist" class="contributor vcard">$songtext_artist</span> - <strong>Songtext <b itemprop="name" class="fn">$songtext_title</b> Lyrics und Liedtexte</strong></h2>
			$duration
			<br /><br />
			<span itemprop="description" lang="$lang">$songtext_content</span>
			<br />
			<a itemprop="url" href="http://$cache_uri">$songtext_artist: $songtext_title Inoffizielle Webseite</a>
			<br />
			<a itemprop="url" href="/search.php?video=$searchsongtext_title">$songtext_artist: $songtext_title als Musik Video suchen</a>
			<br />
		</li>
		<hr border="1" />
	<br />
END;
	} else {
	$mein_text .= <<<END
		<li>
			<h2><span itemprop="byArtist" class="contributor vcard">$songtext_artist</span> - <strong>Songtext <b itemprop="name" class="fn">$songtext_title</b> Lyrics und Liedtexte</strong></h2>
			$duration
			<br /><br />
			<span itemprop="description" lang="$lang">$songtext_content</span>
			<br />
		</li>
		<hr border="1" />
	<br />
END;
	}
} // for ( $ArrayCount=0;

$mein_text .= <<<END
</ul><a class="url" href="https://plus.google.com/102814280381371438232?rel=author" rel="nofollow">Google+ Profil</a> - <a class="url invisible" href="#top" rel="nofollow" style="text-align:center; margin:0 auto">gehe nach oben (Top)</a>	
END;

$content = array_merge(
		array('title_html_de'=>"$title_tag"), 
		array('robots'=>"$robots"),
		array('yt_title'=>"$searchQuery als Songtext ansehen, drucken und teilen"),
		array('content'=>"$mein_text"),
		array('description_de'=>"$title_tag"),
		array('keyword_de'=>"$searchQuery"),
		array('canonical_tag'=>"http://".$cache_uri)
		);
		
$design->setPath( $config->getTemplatePath('index_page') );
$design->display_cache('suche_songtext_de', $content, true, 3600*24*3);

$log->logQuerys( $searchQuery );

exit(0);
?>