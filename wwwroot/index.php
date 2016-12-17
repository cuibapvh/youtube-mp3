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

require_once( "lib/template.inc.php" ); // $function->secureString(
require_once( "lib/config.inc.php" );
require_once( "lib/SSDTube.php" ); 
require_once( "lib/functions.inc.php");
require_once( "lib/connection.inc.php");
require_once( "lib/search.inc.php");
require_once( "lib/mobile/Mobile_Detect.php");
require_once( "lib/geoip.inc.php");

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

$deviceType 		= ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$deviceTypeMobile 	= $detect->isMobile();
$deviceTypeTablet 	= $detect->isTablet();
$countryCode		= $geoip->getCountryCode();	 
$cache_uri 			= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

if ($deviceTypeMobile === TRUE && $deviceTypeTablet === FALSE && preg_match('/(DE|CH|AT|LI)/i',$countryCode)){
	header("HTTP/1.1 301 Moved Permanently");
	// Weiterleitungsziel. Wohin soll eine permanente Weiterleitung erfolgen?
	header("Location: http://www.youtube-mp3.mobi/m/");
	// we have a mobile device
	exit(0);
} else if ($deviceTypeMobile === TRUE && $deviceTypeTablet === FALSE){
	header("HTTP/1.1 301 Moved Permanently");
	// Weiterleitungsziel. Wohin soll eine permanente Weiterleitung erfolgen?
	header("Location: http://www.youtube-mp3.mobi/m/en/");
	// we have a mobile device
	exit(0);
}

// Lyrics Verzeichnis Links auf der Startseite anzeigen
// AA-ZZ combinations
$linking_texts 		= "";
$aacount 			= 1;
$flag 				= 0;
$maxStartDirCount 	= $config->lyrics_startpage_direntry_count();

foreach(range('A', 'Z') as $letter1) {
	foreach(range('A', 'Z') as $letter2) {
        $zufall 		= rand(1,100);
		
		$l 				= strtolower($letter1 . $letter2);
		$lgro			= strtoupper($l);
				
		if ( $aacount <= $maxStartDirCount && $zufall >=97){
			$letter_count	= $function->GetLettersCount($l);
			if ( $letter_count >= 1 && $aacount < $maxStartDirCount) {
				$linking_texts .=<<<LETTER
<a href="/lyrics/verzeichnis.php?l=$l" class="url" itemprop="url" lang="de" title="Ansicht der Lyrics die mit $lgro beginnen">$lgro</a>&nbsp;-&nbsp;
LETTER;
				$aacount++;
			} elseif ( $letter_count >= 1 && $aacount == $maxStartDirCount) {
				$linking_texts .=<<<LETTER
<a href="/lyrics/verzeichnis.php?l=$l" class="url" itemprop="url" lang="de" title="Ansicht der Lyrics die mit $lgro beginnen">$lgro</a>&nbsp;
LETTER;
				$aacount++;
			} // if ( $letter_count >= 1 ) 
			
		} // if ( $aacount <= $maxStartDirCount && $zufall >=97){
	} // foreach(range('A', 'Z') as $letter2) {
} //foreach(range('A', 'Z') as $letter1) {
$linking_texts .= "";

// random songtext
$conn 				= new Connection();
$conn->db( $config->sql_sphinx_dbname() );
$entrys 			= rand(1,$config->yt_songtext_count()); 
$SqlQuery 			= "SELECT artist,title,songtext FROM ".$config->sql_sphinx_table(). " WHERE LENGTH(title)>2 && LENGTH(artist)>2 LIMIT $entrys,1;";
$MySqlArray 		= $conn->doSQLQuery( $SqlQuery );
while( $sql_results = mysql_fetch_array($MySqlArray)) {
	$songtext_artist	= trim($sql_results['artist']);
	$songtext_title		= trim($sql_results['title']);
	$songtext_title_search	= strtolower(str_replace(" ","+",trim($sql_results["title"])));
	$songtext_content	= trim($sql_results['songtext']);
	$songtext_content 	= preg_replace('#(<br */?>\s*)+#i', '<br />', $songtext_content);
}

// random songtext
$lang		= $function->GetLanguageFromString($songtext_content);
$duration	= $function->getDuration($songtext_title);
$desc_title = "Komm und wandel dein YouTube Video legal und kostenlos zu 320 kBit/s MP3! Hier ist das größten Liedtext Verzeichnis der Welt - mit Android APP.";
$desc_title_en = "Come and convert your Youtube Video to 320 Kbit/s quality MP3 Audio the legal way. The biggest Lyrics Archive in the world - with Android App!";

$content 	= array_merge(
	array('title_content'=>"Gib hier deine YouTube Video Webseite ein:<br /> z.B. https://www.youtube.com/watch?v=Kh2FRFhS7QY"), 
	array('title_html_de'=>"Ein legaler YouTube zu MP3 Konverter und Downloader"),
	array('title_html_en'=>"Legal YouTube to MP3 Converter and Downloader"),
	array('linking_text_content'=>$linking_texts),
	array('keyword_de'=>"Youtube MP3 Konverter, $songtext_artist, $songtext_title"),
	array('keyword_en'=>"Youtube MP3 Converter, $songtext_artist, $songtext_title"),
	array('description_de'=>$desc_title),
	array('description_en'=>$desc_title_en),
	array('songtext_artist'=>$songtext_artist),
	array('songtext_title'=>$songtext_title),
	array('songtext_title_search'=>$songtext_title_search),
	array('songtext_content'=>$songtext_content),
	array('songtext_lang'=>$lang),
	array('duration'=>$duration),
	array('canonical_tag'=>"http://".$cache_uri)
);	
	
$design->setPath( $config->getTemplatePath('index_page') );
$design->display_cache('startpage', $content, true, 3600*24*3);
/*
if ( preg_match('/(DE|CH|AT|LI)/i',$countryCode)  ){
	//echo "deutsche sprache: $countryCode";
	$design->display_cache('startpage', $content, true, 3600*24*3);
} else {
	//echo "andere sprache: $countryCode";
	$design->display_cache('startpage_en', $content, true, 3600*24*3);
}
if ($deviceTypeMobile == 1 && $deviceTypeTablet != 1 && preg_match('/(DE|CH|AT|LI)/i',$countryCode)){
	header("HTTP/1.1 301 Moved Permanently");
	// Weiterleitungsziel. Wohin soll eine permanente Weiterleitung erfolgen?
	header("Location: http://www.youtube-mp3.mobi/m/");
	// we have a mobile device
	exit(0);
} else if ($deviceTypeMobile == 1 && $deviceTypeTablet != 1){
	header("HTTP/1.1 301 Moved Permanently");
	// Weiterleitungsziel. Wohin soll eine permanente Weiterleitung erfolgen?
	header("Location: http://www.youtube-mp3.mobi/m/en/");
	// we have a mobile device
	exit(0);
}
*/
exit(0);
?>