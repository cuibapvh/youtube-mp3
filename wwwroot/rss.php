<?php

//error_reporting(E_ALL);
require_once("lib/rss/RSS2Writer.php");  
require_once("lib/functions.inc.php");
require_once("lib/connection.inc.php");

$config = new Config();
$conn 	= new Connection();
$table 	= $config->sql_tablename();

header("Content-Type: text/xml; charset=UTF-8");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$rss = new RSS2Writer(
		'Youtube-MP3.mobi Audio MP3 Download News', 	//Feed Title
		//'Die neusten umgewandelten Youtube Videos als 320 kbit/s MP3 Audio Download', //Feed Description
		'The brand new Music Charts from Youtube as 320 kbit/s MP3 Download with Lyrics', //Feed Description
		'http://www.youtube-mp3.mobi/rss.php', //Feed Link
		6, //indent
		false //Use CDATA
		);
		
$rss->addCategory("RSS Feed");
$rss->channelImage("Youtube MP3 Converter Legal with Android App", "http://www.youtube-mp3.mobi/", "http://www.youtube-mp3.mobi/images/logo_en.png", 177, 106);
$rss->addElement('Copyright', '(c) Youtube-MP3.MOBI, Ltd. 2014');
$rss->addElement('Generator', 'Php RSS2Writer by Daniel Soutter');
	
$conn->db( $config->sql_dbname() );
$SqlQuery 	= "SELECT video_id,video_title FROM $table WHERE mp3_ready=1 ORDER BY RAND() LIMIT 100;";
$MySqlArray = $conn->doSQLQuery( $SqlQuery );

if ( $MySqlArray ) {
	while( $sql_results = mysql_fetch_array($MySqlArray)) {
		$video_id		= $sql_results["video_id"];
		$video_title 	= $sql_results["video_title"];
		//$html_title 	= "Als MP3 mit 320 Kbit/s Downloaden: " .substr($video_title, 0, 85) . " inklusive Songtext & Lyrics";
		$html_title 	= "Download as 320 kbit/s MP3: " .substr($video_title, 0, 85) . " including Lyrics";
		$rss->addItem($video_title, $html_title, "http://www.youtube-mp3.mobi/mp3.php?v=$video_id");
	}
}

echo $rss->getXML();		
exit(0);

?>