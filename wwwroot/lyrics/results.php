<?php

header("HTTP/1.1 301 Moved Permanently");
// Weiterleitungsziel. Wohin soll eine permanente Weiterleitung erfolgen?
header("Location:http://www.youtube-mp3.mobi/lyrics/");
exit;

require_once( "../lib/template.inc.php" ); // $function->secureString(
require_once( "../lib/config.inc.php" );
require_once( "../lib/functions.inc.php");
require_once( "../lib/connection.inc.php");

$function 		= new Functions();
$design 		= new Template();
$config 		= new Config();

$letter		 	= $function->secureString($_REQUEST['l']);
$page 			= $function->secureString($_REQUEST['p']);
$title_dir_tag	= $config->lyrics_dir_title_tag(); 
$page_count		= $function->GetLettersCount($letter);
$cache_uri 	 	= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

if (!isset($page) || !is_numeric($page)){
	$page = 0;
	$title_tag 		= substr($title_dir_tag, 0, 50) . " beim Buchstaben $letter";
	$h1_tag 		= "Lyrics und Songtexte mit dem Anfangsbuchstaben $letter";
} else {
	$title_tag 		= substr($title_dir_tag, 0, 50) . " beim Buchstaben $letter - Seite $page";
	$h1_tag 		= "Lyrics und Songtexte mit dem Anfangsbuchstaben $letter auf Seite $page";
}

if ( $page_count <= 0 || !is_numeric($page_count) ){
	$robots = "NOINDEX,FOLLOW";
} elseif ($page_count > 0 && is_numeric($page_count)) {
	$robots = "INDEX,FOLLOW,ALL";
};

$letters =<<<LETTER
<div class="letters">
<h1>$h1_tag</h1>
LETTER;

// AA-ZZ combinations
$preletter = substr($letter, 0, 1);
foreach(range('A', 'Z') as $letter2) {
	$l 		= strtolower($preletter.$letter2);
	$lgro	= strtoupper($l);
	$letters .=<<<LETTER
<a href="http://www.youtube-mp3.mobi/lyrics/results.php?l=$l" title="Lyrics mit $lgro als Beginn">$lgro</a>&nbsp;
LETTER;
}

$letters .=<<<LETTER
<br /></div><br />
LETTER;
$pages .=<<<PAGES
Seite: Aktuell <div class="strong">
PAGES;

$flag = 0;
for ($i=1;$i<$page_count;$i++){

$pages .=<<<PAGES
<a href="http://www.youtube-mp3.mobi/lyrics/results.php?l=$letter&p=$i" title="$letter Songtext und Lyrics Verzeichnis Seite $i">$i</a>&nbsp;-&nbsp;
PAGES;

	if ( $i > 23 && $flag == 0 ) {
		$pages .= "<br />";
		$flag = 1;
	}
	
	if ( $i > 35 ) {
		
		$pages .=<<<PAGES
<a href="http://www.youtube-mp3.mobi/lyrics/results.php?l=$letter&p=$i" title="$letter Songtext und Lyrics Verzeichnis Seite $i">&gt;</a>&nbsp;</div><br /><br />
PAGES;
		break;
	}
}

$pages .=<<<PAGES
</div><br /><br />
PAGES;

$inhalte	= $function->GetLetterContent($letter,$page);

$content = array_merge(
		array('title_html_de'=>$title_tag), 
		array('robots'=>$robots),
		array('letters'=>$letters),
		array('pages'=>$pages),
		array('content'=>$inhalte),
		array('description_de'=>$title_tag),
		array('keyword_de'=>"Songtext, Lyrics, Verzeichnis"),
		array('yt_title'=>$title_tag),
		array('canonical_tag'=>"http://".$cache_uri)
		);
		
$design->setPath( $config->getTemplatePath('index_page') );
$design->display_cache('directory_list', $content, true, 3600*24*3);

exit(0);

?>