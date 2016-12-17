 <?php

/*
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
//include ("/home/www/lib/security/iosec.php");  //Include module
*/
//later: geo ip und spracheinstellungen
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

require_once( "../lib/template.inc.php" ); // $function->secureString(
require_once( "../lib/config.inc.php" );
require_once( "../lib/SSDTube.php" ); 
require_once( "../lib/functions.inc.php");
require_once( "../lib/connection.inc.php");
require_once( "../lib/search.inc.php");
require_once( "../lib/mobile/Mobile_Detect.php");
require_once( "../lib/geoip.inc.php");
require_once( "../lib/youtube/yt_functions.inc.php");

$detect 	= new Mobile_Detect;
$function 	= new Functions();
$config 	= new Config();
$geoip	 	= new GeoIPClass();
$search 	= new Search();
$design 	= new Template();
$objSSDTube = new SSDTube();
$conn 		= new Connection();
$conn->db( $config->sql_dbname() );
$table 		= $config->sql_tablename();

/*
$deviceTypeMobile 	= $detect->isMobile();
$deviceTypeTablet 	= $detect->isTablet();
$countryCode		= $geoip->getCountryCode();	 


if ($deviceTypeMobile === TRUE && $deviceTypeTablet === FALSE && preg_match('/(DE|CH|AT|LI)/i',$countryCode)){
	header("HTTP/1.1 301 Moved Permanently");
	// Weiterleitungsziel. Wohin soll eine permanente Weiterleitung erfolgen?
	header("Location: http://www.youtube-mp3.mobi/m/");
	// use mobile convert template
	// we have a mobile device
	exit(0);
} else if ($deviceTypeMobile === TRUE && $deviceTypeTablet === FALSE){
	header("HTTP/1.1 301 Moved Permanently");
	// Weiterleitungsziel. Wohin soll eine permanente Weiterleitung erfolgen?
	header("Location: http://www.youtube-mp3.mobi/m/en/");
	// we have a mobile device
	exit(0);
}
*/

$cache_uri 	= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$video 		= urldecode($_REQUEST['video']);
		
$objSSDTube->identify($video, true);
$visitorVideo = $objSSDTube->embed();

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
	array('language'=>"DE"),
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
	array('canonical_tag'=>"http://".$cache_uri)
);

$design->setPath( $config->getTemplatePath('index_page') );
$design->display('content_mobile_de', $content, true);

$conn = new Connection();
$conn->db( $config->sql_dbname() );

$SqlQuery 		= "INSERT INTO $table (video_url,video_id,video_author,video_image,video_duration,video_views,video_title,video_category,video_content,video_embedding) VALUES('$video_url','$video_id','$video_author','$video_image','$video_duration','$video_views','$video_title','$video_category','$video_content','$visitorVideo');";
$conn->doSQLQuery( $SqlQuery );
	
$my_id 			= parseVideoID($video);
$downloadLk 	= getDownloadLink($my_id);
$downloadTitle 	= str_replace(" ", ".", $video_title);
$folder			= $config->download_store_temp_path();
$finalFile		= $folder."/".$downloadTitle.".mp4";

// download file via ipv6
curlGetStore($downloadLk, $finalFile );

$ffmpeg_bin 	= $config->ffmpeg_bin();
$store_path		= $config->mp3_store_path();
$mp3_id			= md5(time().$video.$finalFile.$video_title);
$escapeFile 	= escapeshellcmd($finalFile);
$t 				= system("$ffmpeg_bin -i $escapeFile -acodec libmp3lame -ab 320k $store_path/$mp3_id.mp3", $retVal); 

if ($retVal != 0) {
	$error = "E101: Error Converting To MP3";
	$query_update = "UPDATE converter SET mp3_ready = '0', mp3_error = '$error' WHERE video_url = '$video_url';";
} else {
	$query_update = "UPDATE converter SET mp3_ready = '1', mp3_id = '$mp3_id' WHERE video_url = '$video_url';";
}

$conn = new Connection();
$conn->db( $config->sql_dbname() );
$conn->doSQLQuery( $query_update );

exit(0);
?>