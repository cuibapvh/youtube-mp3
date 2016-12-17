<?php

/*
error_reporting(E_ALL);
ini_set('display_errors', '1');
*/

require_once( "lib/logging.inc.php");
require_once( "lib/template.inc.php" ); // $function->secureString(
require_once( "lib/config.inc.php" );
require_once( "lib/SSDTube.php" ); 
require_once( "lib/functions.inc.php");
require_once( "lib/connection.inc.php");
require_once( "lib/search.inc.php");
require_once( "lib/mobile/Mobile_Detect.php");
require_once( "lib/geoip.inc.php");
require_once( "lib/youtube/yt_functions.inc.php");

$detect 	= new Mobile_Detect;
$function 	= new Functions();
$config 	= new Config();
$geoip	 	= new GeoIPClass();
$search 	= new Search();
$design 	= new Template();
$objSSDTube = new SSDTube();
$conn 		= new Connection();
$log 		= new Logging();

$conn->db( $config->sql_dbname() );
$table 				= $config->sql_tablename();

$deviceTypeMobile 	= $detect->isMobile();
$deviceTypeTablet 	= $detect->isTablet();
$countryCode		= $geoip->getCountryCode();	 

$cache_uri 			= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$video 				= urldecode($_REQUEST['lyrics']);
$langInput			= urldecode($_REQUEST['language']);

$isYoutubeHit		= isYoutubeVideo($video);

if ($isYoutubeHit==0){
	//echo "songtext suche: $video<br>";
	
	$searchQuery 		= ucfirst( urldecode($function->secureString($video)));
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
		$songtext_content 	= preg_replace('#(<br\ ?\/?>)+#i', "<br />", $songtext_content);
		$songtext_content 	= preg_replace('#(<br */?>\s*)+#i', "<br />", $songtext_content);
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
		
} else {
	
	//list($video_v1,) = explode('&', $video);
	//echo "youtube video $video_v1 umwandeln: $video<br>";

	$objSSDTube->identify($video, true);
	$visitorVideo = $objSSDTube->embed();

	// if not valid video url, give error

	$video_image =<<<IMAGE
	<figure><img class="photo" src="$objSSDTube->thumbnail_0_url" width="$objSSDTube->thumbnail_0_width" height="$objSSDTube->thumbnail_0_height" alt="$objSSDTube->title" style="float:left;margin-right:20px;" /></figure>
IMAGE;
	//$video_image 	= str_replace($video_image,"/","");

	$str 				= $function->autolink($objSSDTube->content, array("target"=>"_blank","rel"=>"nofollow"));	
	$IP 				= $function->getip();

	$video_url			= $video;
	$video_author		= $function->secureString($objSSDTube->author);
	$video_duration		= $function->secureString($objSSDTube->duration);
	$video_views		= $function->secureString($objSSDTube->viewcount);
	$video_title		= $objSSDTube->title;
	$video_category		= $function->secureString($objSSDTube->category);
	$video_content		= $function->secureString($str);
	$video_id			= md5(time() . date("Ymdhis") .$video_title.$video_embedding.$video_content.$video_category.$video_views.$IP.crypt(uniqid(rand(),1)).uniqid(rand(),true));

	$video_title 		= str_replace("&","",$video_title );
	$searchTitle 		= trim(preg_replace("/\([^)]+\)/","", $video_title));
	$SongtextRawContent = $search->SphinxSearch($searchTitle);
	list( $songtext_title,$songtext_artist,$songtext_content ) = explode('#####', $SongtextRawContent[0] );
	$lang				= $function->GetLanguageFromString($songtext_content);

	if (strlen($songtext_title)<2){
		$songtext_title = $video_title;
	} 
	if (strlen($songtext_artist)<2){
		list($songtext_artist,) = explode("-",$searchTitle);
	}

	$html_title 	= substr($video_title, 0, 50) . " zu MP3 umwandeln";
	$desc_title 	= substr($songtext_title, 0, 50) . " als MP3 downloaden";
	$html_title_en 	= substr($video_title, 0, 50) . " convert to MP3";
	$desc_title_en 	= substr($songtext_title, 0, 50) . " download as MP3";
	$duration		= $function->getDuration($songtext_title);

	$content = array_merge(
		array('title_content'=>$video_title), 
		array('image_content'=>$video_image), 
		array('author_content'=>$video_author), 
		array('category_content'=>$video_category), 
		array('lenght_content'=>$video_duration), 
		array('views_content'=>$video_views), 
		array('raw_content'=>$str), 
		array('embedding_content'=>$visitorVideo),
		array('title_html_de'=>$html_title),
		array('title_html_en'=>$html_title_en),
		array('video_id'=>$video_id),
		array('video_url'=>$video_url),
		array('keyword_de'=>"$video_title, $html_title, $songtext_title, "),
		array('keyword_en'=>"$video_title, $html_title_en, $songtext_title, "),
		array('description_de'=>$desc_title),
		array('description_en'=>$desc_title_en),
		array('songtext_artist'=>$songtext_artist),
		array('songtext_title'=>$songtext_title),
		array('songtext_content'=>$songtext_content),
		array('songtext_lang'=>$lang),
		array('duration'=>$duration),
		array('framesource'=>"http://www.youtube-mp3.mobi/convert_frame.php?video=$video"),
		array('canonical_tag'=>"http://".$cache_uri)
	);

	$design->setPath( $config->getTemplatePath('index_page') );

	if ($deviceTypeMobile === TRUE && $deviceTypeTablet === FALSE && preg_match('/(DE|CH|AT|LI)/i',$countryCode)){
		//header("HTTP/1.1 301 Moved Permanently");
		// Weiterleitungsziel. Wohin soll eine permanente Weiterleitung erfolgen?
		//header("Location: http://www.youtube-mp3.mobi/m/");
		// use mobile convert template
		// we have a mobile device
		$design->display_cache('content_mobile_de', $content, true, 3600*24*3);
	} else if ($deviceTypeMobile === TRUE && $deviceTypeTablet === FALSE){
		//header("HTTP/1.1 301 Moved Permanently");
		// Weiterleitungsziel. Wohin soll eine permanente Weiterleitung erfolgen?
		//header("Location: http://www.youtube-mp3.mobi/m/en/");
		// we have a mobile device
		$design->display_cache('content_mobile_en', $content, true, 3600*24*3);
	} else if ( preg_match('/(DE|CH|AT|LI)/i',$countryCode)){
		//echo "deutsche sprache: $countryCode";
		$design->display_cache('convert_de', $content, true, 3600*24*3);
	} else {
		//echo "andere sprache: $countryCode";
		$design->display_cache('convert_en', $content, true, 3600*24*3);
	}

	$conn = new Connection();
	$conn->db( $config->sql_dbname() );

	$SqlQuery 		= "INSERT INTO $table (video_url,video_id,video_author,video_image,video_duration,video_views,video_title,video_category,video_content,video_embedding) VALUES('$video_url','$video_id','$video_author','$video_image','$video_duration','$video_views','$video_title','$video_category','$video_content','$visitorVideo');";
	$conn->doSQLQuery( $SqlQuery );
}
$log->logQuerys( $video );
exit(0);
?>