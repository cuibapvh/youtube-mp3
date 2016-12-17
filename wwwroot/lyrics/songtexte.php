<?php
require_once("../lib/template.inc.php"); 
require_once("../lib/config.inc.php");
require_once("../lib/functions.inc.php");
require_once("../lib/connection.inc.php");
require_once( "../lib/mobile/Mobile_Detect.php");
require_once( "../lib/geoip.inc.php");

$function 		= new Functions();
$design 		= new Template();
$config 		= new Config();
$detect 		= new Mobile_Detect;
$geoip	 		= new GeoIPClass();

$deviceTypeMobile 	= $detect->isMobile();
$deviceTypeTablet 	= $detect->isTablet();
$countryCode		= $geoip->getCountryCode();

$letter		 	= urldecode($function->secureString($_REQUEST['l']));
$shortid 		= urldecode($_REQUEST['w']);
$cache_uri 	 	= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$ResultsArray 	= $function->GetSongtextContent($letter,$shortid);

list($title,$artist,$songtext,$lang) = explode("#####",$ResultsArray[count($ResultsArray)-1]);
$lang			= $function->GetLanguageFromString($songtext);
$st 			= "$artist - $title";
$songtext_title_search	= strtolower(str_replace(" ","+",$st));
$title_dir_tag	= "$artist: $title Songtexte";
$title_tag 		= substr($title_dir_tag, 0, 68); // . " im Lyrics Verzeichnis anschauen";
$title_dir_tag_en = "$artist: $title Lyrics";
$title_tag_en 	= substr($title_dir_tag_en, 0, 68); // . " im Lyrics Verzeichnis anschauen";
$duration		= $function->getDuration($title);
$count			= count($ResultsArray);
$canonicalUrl	= strtolower(trim($function->secureString($st)));
$canonicalUrl	= str_replace(" ","+",$canonicalUrl );



if ( $count <= 0 || !is_numeric($count) ){
	$robots = "NOINDEX,FOLLOW";
} elseif ( $count > 0 && is_numeric($count)) {
	$robots = "INDEX,FOLLOW,ALL";
};

$songtext		= $function->clearUTF($songtext);
$songtext_tmp 	= $songtext;
$songtext_tmp 	= preg_replace('#(<br\ ?\/?>)+#i', ",", $songtext_tmp); 
$songtext_tmp 	= preg_replace('#(<br */?>\s*)+#i', ",", $songtext_tmp); 
$excerpt 		= substr($songtext_tmp, 0,140);

$content = array_merge(
		array('title_html_de'=>$title_tag), 
		array('title_html_en'=>$title_tag_en), 
		array('robots'=>$robots),
		array('description_de'=>"Songtext: $excerpt"),
		array('description_en'=>"Lyrics: $excerpt"),
		array('keyword_de'=>"$title_dir_tag, $artist, Songtext, Liedtexte"),
		array('keyword_en'=>"$title_dir_tag_en, $artist, Lyrics"),
		array('yt_title'=>$title_dir_tag),
		array('yt_title_en'=>$title_dir_tag_en),
		array('songtext_artist'=>trim($artist)),
		array('songtext_title'=>trim($title)),
		array('songtext_content'=>trim($songtext)),
		array('songtext_lang'=> trim($lang)),
		array('duration'=>$duration),
		array('songtext_title_search'=>$songtext_title_search),
		array('canonical_tag'=>"http://www.youtube-mp3.mobi/songtext.php?lyrics=$canonicalUrl")
		);
		
$design->setPath( $config->getTemplatePath('index_page') );
//$design->display_cache('directory_show', $content, true, 3600*24*3);
if ($deviceTypeMobile === TRUE && $deviceTypeTablet === FALSE && preg_match('/(DE|CH|AT|LI)/i',$countryCode)){
	//header("HTTP/1.1 301 Moved Permanently");
	// Weiterleitungsziel. Wohin soll eine permanente Weiterleitung erfolgen?
	//header("Location: http://www.youtube-mp3.mobi/m/");
	// use mobile convert template
	// we have a mobile device
	$design->display_cache('directory_show_mobile_de', $content, true, 3600*24*3);
} else if ($deviceTypeMobile === TRUE && $deviceTypeTablet === FALSE){
	//header("HTTP/1.1 301 Moved Permanently");
	// Weiterleitungsziel. Wohin soll eine permanente Weiterleitung erfolgen?
	//header("Location: http://www.youtube-mp3.mobi/m/en/");
	// we have a mobile device
	$design->display_cache('directory_show_mobile_en', $content, true, 3600*24*3);
} else if ( preg_match('/(DE|CH|AT|LI)/i',$countryCode)){
	//echo "deutsche sprache: $countryCode";
	$design->display_cache('directory_show', $content, true, 3600*24*3);
} else {
	//echo "andere sprache: $countryCode"; // later: directory_show_en
	$design->display_cache('directory_show_en', $content, true, 3600*24*3);
}
exit(0);

?>