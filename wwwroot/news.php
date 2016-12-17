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
require_once( "lib/functions.inc.php");
require_once( "lib/connection.inc.php");

$function 	= new Functions();
$config 	= new Config();
$design 	= new Template();
$conn 		= new Connection();
$conn->db( $config->sql_dbname() );
$table 		= $config->sql_tablename();

// Lyrics Verzeichnis Links auf der Startseite anzeigen
// AA-ZZ combinations
$linking_texts 		.= "<p><h2><strong><i>Songtexte und Lyrics Verzeichnis Auszug</i></strong></h2><br /><h3>";
$aacount 			= 1;
$flag 				= 0;
$maxStartDirCount 	= $config->lyrics_startpage_direntry_count();
$cache_uri 			= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

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
$linking_texts .= "</h3><p/>";

// mp3.php more results: START
$conn 		= new Connection();
$conn->db( $config->sql_dbname() );

//$SqlQuery 	= "SELECT video_id,video_title FROM $table WHERE mp3_ready = '1' ORDER BY RAND() LIMIT 0,15;"; // bei zuvielen einträgen php random
$SqlQuery 	= "SELECT video_id,video_title FROM $table WHERE 1=1 ORDER BY RAND() LIMIT 0,300;";
$MySqlArray = $conn->doSQLQuery( $SqlQuery );
$count = 0;
$linking_texts .= "<p><ul><h2><strong>Mehr umgewandelte MP3s mit Liedtexten downloaden:</strong></h2><br />";

if ( $MySqlArray ) {
	while( $sql_results = mysql_fetch_array($MySqlArray)) {
		$video_id		= $sql_results["video_id"];
		$video_title 	= $sql_results["video_title"];
		$html_title 	= substr($video_title, 0, 130) . " als MP3 downloaden";
		if ( $count < 14 && strlen($video_title) > 3){
			$linking_texts .= <<< EOT
<li><h3><strong><b><a class="url" href="/mp3.php?v=$video_id" rel="me" itemprop="url" lang="de">$html_title</a></strong></b></h3></li>
EOT;
		} elseif ( $count > 14 && $count <= 27 && strlen($video_title) > 3){
			$linking_texts .= <<< EOT
<li><strong><b><a class="url" href="/mp3.php?v=$video_id" rel="me" itemprop="url" lang="de">$html_title</a></strong></b></li>
EOT;
		} elseif ( $count > 27 && $count <= 43 && strlen($video_title) > 3 ){
			$linking_texts .= <<< EOT
<li><strong><a class="url" href="/mp3.php?v=$video_id" rel="me" itemprop="url" lang="de">$html_title</a></strong></li>
EOT;
		} elseif ( $count > 43 && $count <= 53 && strlen($video_title) > 3 ){
			$linking_texts .= <<< EOT
<li><b><a class="url" href="/mp3.php?v=$video_id" rel="me" itemprop="url" lang="de">$html_title</a></b></li>
EOT;
		} elseif ( $count > 53 && $count <= 67 && strlen($video_title) > 3 ){
			$linking_texts .= <<< EOT
<li><a class="url" href="/mp3.php?v=$video_id" rel="me" itemprop="url" lang="de">$html_title</a></li>
EOT;
		}
		$count++;
	}; # while( $sql_results = mysql_fetch_array($results)) { }
	$linking_texts .="<p/></ul>";
}
// mp3.php more results: END
	
	$content 	= array_merge(
		array('title_html_de'=>"Die aktuellsten MP3 Chart Downloads!"), 
		
		array('linking_text_content'=>$linking_texts),
		array('keyword_de'=>"Youtube Video News, MP3 Download News, $songtext_artist, $songtext_title"),
		array('description_de'=>"Hier findest du die neusten Youtube Videos umgewandelt und verfügbar als MP3 Download für Desktop, Tablet und Smartphone."),
		array('linking_text_content'=>$linking_texts),
		array('canonical_tag'=>"http://".$cache_uri)
	);	

$design->setPath( $config->getTemplatePath('index_page') );
$design->display_cache('news_de', $content, true, 3600*24*3);

exit(0);
?>