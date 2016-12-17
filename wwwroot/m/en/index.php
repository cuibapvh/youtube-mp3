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


require_once( "/home/wwwyoutube/lib/template.inc.php" ); // $function->secureString(
require_once( "/home/wwwyoutube/lib/config.inc.php" );
$design 	= new Template();
$config 	= new Config();

$desc_title_en = "Come and convert your Youtube Video to 192 Kbit/s quality MP3 Audio. Combined with the biggest Songtext Archive in the world - with Android App!";

$content = array_merge(
		array('title_content'=>$video_title), 
		array('title_html_en'=>"YouTube MP3 Converter - Mobile Webpage"),
		array('image_content'=>$video_image), 
		array('author_content'=>$video_author), 
		array('category_content'=>$video_category), 
		array('lenght_content'=>$video_duration), 
		array('views_content'=>$video_views), 
		array('raw_content'=>$str), 
		array('embedding_content'=>$visitorVideo),
		array('title_html_de'=>"Youtube MP3 Konverter - Mobile Webseite"),
		array('video_id'=>$video_id),
		array('video_url'=>$video_url),
		array('keyword_en'=>"legal youtube,legal youtube download, "),
		array('description_de'=>$desc_title),
		array('songtext_artist'=>$songtext_artist),
		array('songtext_title'=>$songtext_title),
		array('songtext_content'=>$songtext_content),
		array('songtext_lang'=>$lang),
		array('duration'=>$duration),
		array('description_en'=>$desc_title_en),
		array('canonical_tag'=>"http://".$cache_uri)
	);
	
$design->setPath( $config->getTemplatePath('index_page') );
$design->display_cache('startpage_mobile_en', $content, true, 3600*24*3);

exit(0);
?>