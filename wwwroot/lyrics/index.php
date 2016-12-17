<?php
require_once("../lib/template.inc.php"); 
require_once("../lib/config.inc.php");
require_once("../lib/functions.inc.php");
//require_once("../lib/connection.inc.php");

$function 		= new Functions();
$design 		= new Template();
$config 		= new Config();

$letter		 	= $function->secureString(urldecode($_REQUEST['l']));
$page 			= $function->secureString(urldecode($_REQUEST['p']));
$title_dir_tag	= $config->lyrics_dir_title_tag(); 
$cache_uri 	 	= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

if (!isset($page) || !is_numeric($page)){
	$page = 0;
	$title_tag 		= substr($title_dir_tag, 0, 50); // . " - MP3 umwandeln";
} else {
	$title_tag 		= substr($title_dir_tag, 0, 50) . " - Seite $page";
}

$count			= 1;
if ( $count <= 0 || !is_numeric($count) ){
	$robots = "NOINDEX,FOLLOW";
} elseif ( $count > 0 && is_numeric($count)) {
	$robots = "INDEX,FOLLOW,ALL";
};
// source: http://www.viva.tv/musikvideo/1756-top-100-single-jahrescharts-2013/playlist
$letters =<<<LETTER
<h3 itemprop="description" style="font-size: 14px;"><strong>Wir haben Musik Liedtexte von verschiedenen aktuellen Chart Artisten im Angebot. Komm und lade dir das Youtube Video herunter und lies dazu den passenden Liedtext. Die Auswahl an Lyrics ist grenzenlos! Komm st&ouml;bern...</strong><br />
	<ul style="font-size: 16px;">
		<li><b><strong>Helene Fischer - Atemlos Durch Die Nacht</strong></b></li>
		<li><b><strong>Mr. Probz - Waves</strong></b></li>
		<li><b><strong>Pharrell Williams - Happy</strong></b></li>
		<li><strong>Ed Sheeran - I See Fire</strong></li>
		<li><strong>Faul & Wad Ad vs. Pnau - Changes</strong></li>
		<li><strong>Pitbull feat. Kesha - Timber</strong></li>
		<li><b>Avicii - Addicted To You</b></li>
		<li><b>Shakira feat. Rihanna - Cant Remember To Forget You</b></li>
		<li><b>Marteria - Kids (2 Finger An Den Kopf)</b></li>
		<li><i>James Arthur - Impossible</i></li>
		<li><i>Martin Garrix - Animals</i></li>
		<li><i>Avicii - Hey Brother</i></li>
		<li>Lorde - Royals</li>
		<li>DVBBS & Borgeous - Tsunami (Original Mix)</li>
		<li>Helene Fischer - Ich Will Immer Wieder... Dieses Fieber Spür'n</li>
	</ul>
</h3>
<h4 style="font-size: 12px;">Hier findest du eine Ansicht von Lyrics und Songtexten mit den jeweiligen Anfangsbuchstaben:</h4>
<div class="letters">
LETTER;


// AA-ZZ combinations
$aacount 			= 1;
$maxStartDirCount 	= 12; //$config->lyrics_startpage_direntry_count();

foreach(range('A', 'Z') as $letter1) {
	foreach(range('A', 'Z') as $letter2) {
		
		$l 				= strtolower($letter1 . $letter2);
		$lgro			= strtoupper($l);		
		$letter_count	= $function->GetLettersCount($l);
		
		if ( $letter_count >= 1 && $aacount < $maxStartDirCount) {
			$letters .=<<<LETTER
<a href="/lyrics/verzeichnis.php?l=$l" class="url" itemprop="url" lang="de" title="Songtexte die mit $lgro anfangen">$lgro</a>&nbsp;&nbsp;
LETTER;
		} elseif ( $letter_count >= 1 && $aacount >= $maxStartDirCount) {
			$letters .=<<<LETTER
<a href="/lyrics/verzeichnis.php?l=$l" class="url" itemprop="url" lang="de" title="Songtexte die mit $lgro anfangen">$lgro</a>&nbsp;<br />
LETTER;
			$aacount = 0;
		} elseif ( $letter_count <= 0 ) {
			$letters .=<<<LETTER
				<span class="whites">__</span>
LETTER;
		}
		$aacount++;
	}
}

$letters .=<<<LETTER
</div><br /><br />
LETTER;

$content = array_merge(
		array('title_html_de'=>$title_tag), 
		array('robots'=>$robots),
		array('letters'=>$letters),
		array('description_de'=>"Im Liedtext Verzeichnis findest du die aktuellsten Charts, Musik und Videos mit Songtexten von Künstlern und Artisten die weltweit bekannt sind."),
		array('keyword_de'=>"Songtext, Lyrics, Verzeichnis"),
		array('yt_title'=>$title_dir_tag),
		array('canonical_tag'=>"http://".$cache_uri)
		);
		
$design->setPath( $config->getTemplatePath('index_page') );
$design->display_cache('directory', $content, true, 3600*24*3);

exit(0);

?>