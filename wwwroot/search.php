<?php

/*
error_reporting(E_ALL);
ini_set('display_errors', '1');
*/
require_once( "lib/config.inc.php" );
require_once( "lib/template.inc.php" );
require_once( "lib/functions.inc.php");
require_once( "lib/search.inc.php");
require_once( "lib/SSDTube.php" ); 
require_once( "lib/logging.inc.php" );

$search 		= new Search();
$function 		= new Functions();
$objSSDTube 	= new SSDTube();
$design 		= new Template();
$config 		= new Config();
$log			= new Logging();

$searchQuery 	= ucfirst( urldecode($function->secureString($_REQUEST['video'])));
$page 			= urldecode($function->secureString($_REQUEST['p']));

if (!isset($page) || !is_numeric($page)){
	$page = 0;
	$title_tag 		= substr($searchQuery, 0, 50) . " Videos zu MP3 umwandeln";
} else {
	$title_tag 		= substr($searchQuery, 0, 50) . " Youtube Videos - Seite $page";
}

$CurrentPage = $page;
$PageNext	 = $CurrentPage + 1;
$PageBack	 = $CurrentPage - 1;

if ( $PageBack <= -1 ) {
	$PageBack = 0;
};

$ResultsArray 	= $search->ytSearch($searchQuery, $page);
$count			= count($ResultsArray);

if ( $count <= 0 || !is_numeric($count) ){
	$robots = "NOINDEX,FOLLOW";
} elseif ( $count > 0 && is_numeric($count)) {
	$robots = "INDEX,FOLLOW,ALL";
};

$vor				= "/search.php?video=".strtolower(str_replace(" ","+",trim($searchQuery)))."&p=$PageNext";
$zurck				= "/search.php?video=".strtolower(str_replace(" ","+",trim($searchQuery)))."&p=$PageBack";
$navi 				= "<h4 style=\"font-size:14pt;\"><center><b><a class=\"url\" href=\"$vor\" rel=\"next\"> N&auml;chste Seite </a> - <a class=\"url\" href=\"$zurck\" rel=\"prev\"> Vorherige Seite </a></b></center></h4>";
$mein_text 			= "";
$desc_title 		= substr($searchQuery, 0, 50) . " Youtube Video als MP3 downloaden";

$searchTitle 		= trim(preg_replace("/\([^)]+\)/","", $searchQuery)); // http://stackoverflow.com/questions/2174362/remove-text-between-parentheses-php
$searchTitle 		= preg_replace('/\W/',' ', $searchTitle);

$SongtextRawContent = $search->SphinxSearch( $function->secureString($searchTitle) );
list( $songtext_title,$songtext_artist,$songtext_content ) = explode('#####', $SongtextRawContent[0] );
$songtext_content 	= preg_replace('#(<br\ ?\/?>)+#i', "<br />", $songtext_content); # 
$songtext_content 	= preg_replace('#(<br */?>\s*)+#i', "<br />", $songtext_content); # 
$cache_uri 			= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$duration			= $function->getDuration($songtext_title);
$lang				= $function->GetLanguageFromString($songtext_content);

if (strlen($songtext_title)<2){
	$songtext_title = $searchQuery;
} 
if (strlen($songtext_artist)<2){
	$songtext_artist = "Allgemein";
}
	
for ( $ArrayCount=0; $ArrayCount<=$count - 1; $ArrayCount++ ) {
	
	list( $title,$link,$author ) = explode('#####', $ResultsArray[$ArrayCount] );
	$title = trim($title);
	$link = trim($link);
	$author = trim($author);
	$objSSDTube->identify($link, true);
	$visitorVideo = $objSSDTube->embed();
	$video_image =<<<IMAGE
<figure><img class="photo" src="$objSSDTube->thumbnail_0_url" width="$objSSDTube->thumbnail_0_width" height="$objSSDTube->thumbnail_0_height" alt="$objSSDTube->title" style="float:left;margin-right:20px;" /></figure>
IMAGE;
	$video_content  = $function->autolink($objSSDTube->content, array("target"=>"_blank","rel"=>"nofollow"));	
	$video_duration	= $objSSDTube->duration;
	$video_views	= $objSSDTube->viewcount;
	$video_category	= $objSSDTube->category;
	$lyrics_search 	= "/songtext.php?lyrics=" . strtolower(str_replace(" ","+",trim($title)));
	
	$my_extra_songtext = "";
	if ( $ArrayCount == 0 ){
	
		$my_extra_songtext =<<<END2
		<li itemscope itemtype="http://schema.org/MusicRecording" class="haudio">
			<h2><span itemprop="byArtist" class="contributor">$songtext_artist</span> - <strong>Songtext <b itemprop="name" class="fn">$songtext_title</b> Lyrics</strong></h2>
			$duration - <a class="url" itemprop="url" href="http://$cache_uri">Inoffizielle Webseite</a>
			<p itemprop="description" lang="$lang">$songtext_content</p>
		</li>
END2;

	};
	
	$mein_text .= <<<END
	<p>
		<ul style="list-style: none;">
			<li class="fn"><h2><strong><b>$title Youtube Videos</b></strong></h2></li>
			<li>$visitorVideo</li>
			<li>
				<b>
					<form method="GET" action="/songtext.php">
						<input type="hidden" name="lyrics" id="video" value="$link" />
						<input type="submit" value="$title Youtube Video zu MP3 umwandeln" class="button" />
						<input type="hidden" id="language" value="EN" />
					</form>
				</b>
			</li>
			<li>
				<span class="contributor vcard">
					Author: <span class="fn">$author</span>
				</span>
			</li>
			<li>Video L&auml;nge: $video_duration Sekunden</li>
			<li>Views: $video_views</li>
			<li>Passenden Songtext suchen: <a href="$lyrics_search" rel="prefetch search">Such hier den Songtext $title als Text Lyrics</a></li>
			$my_extra_songtext
		</ul>
	</p>
	<br />
END;
}

$songtext_tmp 	= $songtext_content;
$songtext_tmp 	= preg_replace('#(<br\ ?\/?>)+#i', ",", $songtext_tmp); 
$songtext_tmp 	= preg_replace('#(<br */?>\s*)+#i', ",", $songtext_tmp); 
$excerpt 		= substr($songtext_tmp, 0,75);

$content = array_merge(
		array('title_html_de'=>"$title_tag"), 
		array('robots'=>"$robots"),
		array('yt_title'=>"$searchQuery als Youtube Video anschauen, mit MP3 umwandeln und mit Songtext integriert downloaden"),
		array('content'=>"$mein_text"),
		array('description_de'=>"$desc_title - $excerpt"),
		array('navi'=>"$navi"),
		array('keyword_de'=>"$searchQuery"),
		array('canonical_tag'=>"http://".$cache_uri)
		);
		
$design->setPath( $config->getTemplatePath('index_page') );
$design->display_cache('search_de', $content, true, 3600*24*3);

if (strlen($searchQuery) >= 2 ){
	$log->logQuerys( $searchQuery );
}; # if ($ResultCount >= 20 ){

exit(0);
?>