<?php

require_once( "../lib/template.inc.php" );
require_once( "../lib/config.inc.php" );
require_once( "../lib/functions.inc.php");

$function 	= new Functions();
$config 	= new Config();
$design 	= new Template();

$cache_uri 	= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$desc_title = "";
$content 	= array_merge(
	array('title_content'=>"Gib hier deine YouTube Video Webseite ein:<br /> z.B. https://www.youtube.com/watch?v=Kh2FRFhS7QY"), 
	array('title_html_de'=>"YouTube MP3 Konverter - Android Apps bei Google Play"),
	array('keyword_de'=>"Youtube Video Downloader, Musik Video Charts, Android App, youtube mp3, songtext, lyrics"),
	array('description_de'=>"Lade dir die kostenlose Youtube Musik Videos App herunter und streame und downloade dir die Top 100 German Charts."),
	array('canonical_tag'=>"http://".$cache_uri)
);	
$design->setPath( $config->getTemplatePath('index_page') );
$design->display_cache('app_de', $content, true, 3600*24*3);

exit(0);
?>