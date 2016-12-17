<?php

require_once( "lib/functions.inc.php");
require_once( "lib/template.inc.php" );
require_once( "lib/config.inc.php" );
require_once( "lib/connection.inc.php");
require_once( "lib/search.inc.php");

$function	= new Functions();
$search 	= new Search();
$config 	= new Config();
$design 	= new Template();
$conn 		= new Connection();
$conn->db( $config->sql_dbname() );
$table 		= $config->sql_tablename();
$cache_uri 	= $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

// build extra content links	
$SqlQuery 	= "SELECT video_id,video_title FROM $table WHERE 1=1 ORDER BY RAND() LIMIT 0,15;"; // bei zuvielen einträgen php random
$MySqlArrayLINKING = $conn->doSQLQuery( $SqlQuery );
$count 		= 0;
$linking_texts = "<ul>Noch mehr Audio Musik MP3s downloaden:";

if ( $MySqlArrayLINKING ) {
	while( $sql_results = mysql_fetch_array($MySqlArrayLINKING)) {
		$video_id		= $sql_results["video_id"];
		$video_title 	= $sql_results["video_title"];
		$html_title 	= substr($video_title, 0, 50) . " als MP3 downloaden";
		if ( $count < 4 && strlen($video_title) > 3){
			$linking_texts .= <<< EOT
<li><h3><strong><b><a class="url" href="/mp3.php?v=$video_id" itemprop="url" rel="me">$html_title</a></strong></b></h3></li>
EOT;
		} elseif ( $count > 4 && $count <= 7 && strlen($video_title) > 3){
			$linking_texts .= <<< EOT
<li><strong><b><a class="url" href="/mp3.php?v=$video_id" itemprop="url" rel="me">$html_title</a></strong></b></li>
EOT;
		} elseif ( $count > 7 && $count <= 10 && strlen($video_title) > 3 ){
			$linking_texts .= <<< EOT
<li><strong><a class="url" href="/mp3.php?v=$video_id" itemprop="url" rel="me">$html_title</a></strong></li>
EOT;
		} elseif ( $count > 10 && $count <= 13 && strlen($video_title) > 3 ){
			$linking_texts .= <<< EOT
<li><b><a class="url" href="/mp3.php?v=$video_id" itemprop="url" rel="me">$html_title</a></b></li>
EOT;
		} elseif ( $count > 13 && $count <= 17 && strlen($video_title) > 3 ){
			$linking_texts .= <<< EOT
<li><a class="url" href="/mp3.php?v=$video_id" itemprop="url" rel="me">$html_title</a></li>
EOT;
		}
		$count++;
	}; # while( $sql_results = mysql_fetch_array($results)) { }
	
};
		$linking_texts .="</ul>";


// Lyrics Verzeichnis Links auf der Startseite anzeigen
// AA-ZZ combinations
$linking_texts 		.= "<p><h2><strong><i>Songtexte und Lyrics Verzeichnis</i></strong></h2><h3>";
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
$linking_texts .= "</h3><p/>";

		
		/*
// songtexts more results: START
$conn 		= new Connection();
$conn->db( $config->sql_sphinx_dbname() );
$maxresults 	= $config->yt_result_count_max();
$entrys 		= rand(1,$config->yt_songtext_count()); 
$SqlQuery 		= "SELECT title FROM ".$config->sql_sphinx_table()." WHERE id >0 LIMIT $entrys,$maxresults;";
$MySqlArray222  = $conn->doSQLQuery( $SqlQuery );
$count 			= 0;	

$linking_texts .= "<ul>Noch mehr Youtube Videos suchen, umwandeln und mit Songtext downloaden:";
if ( $MySqlArray222 ) {
	while( $sql_results = mysql_fetch_array($MySqlArray222)) {
		$video_title	= strtolower(str_replace(" ","+",trim($sql_results["title"])));
		$html_title 	= substr(trim($sql_results["title"]), 0, 50) . " als Youtube Video suchen";
		if ( $count < 2 && strlen($video_title) > 3){
			$linking_texts .= <<< EOT
				<li><h4><strong><b><a class="url" href="/search.php?video=$video_title" rel="me" itemprop="url">$html_title</a></strong></b></h4></li>
EOT;
		} elseif ( $count > 2 && $count <= 5 && strlen($video_title) > 3){
			$linking_texts .= <<< EOT
<li><strong><b><a class="url" href="/search.php?video=$video_title" rel="me" itemprop="url">$html_title</a></strong></b></li>
EOT;
		} elseif ( $count > 5 && $count <= 7 && strlen($video_title) > 3 ){
			$linking_texts .= <<< EOT
<li><strong><a class="url" href="/search.php?video=$video_title" rel="me" itemprop="url">$html_title</a></strong></li>
EOT;
		} elseif ( $count > 7 && $count <= 10 && strlen($video_title) > 3 ){
			$linking_texts .= <<< EOT
<li><b><a class="url" href="/search.php?video=$video_title" rel="me" itemprop="url">$html_title</a></b></li>
EOT;
		} elseif ( $count > 10 && $count <= 12 && strlen($video_title) > 3 ){
			$linking_texts .= <<< EOT
<li><a class="url" href="/search.php?video=$video_title" rel="me" itemprop="url">$html_title</a></li>
EOT;
		}
		$count++;
	}; # while( $sql_results = mysql_fetch_array($results)) { }
	$linking_texts .="</ul>";
}
// songtexts more results: END
*/

	
// build content from mysql
$video_id_real 	= $function->secureString($_REQUEST['v']);
if(!preg_match('/^[a-f0-9]{32}$/', $video_id_real)){
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://www.youtube-mp3.mobi/");
	exit(0);
}



$conn 			= new Connection();
$conn->db( $config->sql_dbname() );
$SqlQuery 		= "SELECT * FROM $table WHERE video_id = '$video_id_real' LIMIT 1;";
$MySqlArray 	= $conn->doSQLQuery( $SqlQuery );

if ( $MySqlArray ) {
	while( $sql_results = mysql_fetch_array($MySqlArray)) {
		$video_id 			= $sql_results["video_id"];
		$video_author 		= $sql_results["video_author"];
		$video_image 		= $sql_results["video_image"];
		$video_duration 	= $sql_results["video_duration"];
		$video_views 		= $sql_results["video_views"];
		$video_title 		= $sql_results["video_title"];
		$video_category 	= $sql_results["video_category"];
		$video_content 		= $sql_results["video_content"];
		$video_embedding 	= $sql_results["video_embedding"];
		$mp3_ready 			= $sql_results["mp3_ready"];
		$mp3_id 			= $sql_results["mp3_id"];
		$mp3_error 			= $sql_results["mp3_error"];	
	}; # while( $sql_results = mysql_fetch_array($results)) { }
	
	$searchTitle 		= trim(preg_replace("/\([^)]+\)/","", $video_title));
	$SongtextRawContent = $search->SphinxSearch($searchTitle);
	list( $songtext_title,$songtext_artist,$songtext_content ) = explode('#####', $SongtextRawContent[0] );
	$songtext_content 	= preg_replace('#(<br */?>\s*)+#i', '<br />', $songtext_content);
	$lang				= $function->GetLanguageFromString($songtext_content);
	//$lyrics_search 		= "/songtext.php?lyrics=" . strtolower(str_replace(" ","+",trim($video_title)));
	
	if (strlen($songtext_title)<2){
		$songtext_title = $video_title;
	} 
	if (strlen($songtext_artist)<2){
		list($songtext_artist,) = explode("-",$searchTitle);
	}
	
	$duration		= $function->getDuration($songtext_title);
	$html_title 	= substr($video_title, 0, 50) . " als MP3 downloaden";
	$desc_title 	= substr($songtext_title, 0, 140) . " als MP3 downloaden";
	$video_title_url= strtolower(str_replace(" ","+",trim($video_title)));
	
	if (strlen($video_id) == 32 && strlen($mp3_id) == 32 && $mp3_ready == 1 ){

		$content = array_merge(
			array('title_content'=>$video_title), 
			array('image_content'=>$video_image), 
			array('author_content'=>$video_author), 
			array('category_content'=>$video_category), 
			array('lenght_content'=>$video_duration), 
			array('views_content'=>$video_views), 
			array('raw_content'=>$video_content), 
			array('embedding_content'=>$video_embedding),
			array('title_html_de'=>$html_title),
			array('video_id'=>$video_id),
			array('video_url'=>$video_url),
			array('robots'=>"index,follow,all"),
			array('linking_text_content'=>$linking_texts),
			array('keyword_de'=>"$video_title, $html_title, $songtext_title"),
			array('description_de'=>$desc_title),
			array('songtext_artist'=>$songtext_artist),
			array('songtext_title'=>$songtext_title),
			array('songtext_content'=>$songtext_content),
			array('songtext_lang'=>$lang),
			array('songtext_title_search'=>$video_title_url),
			array('duration'=>$duration),
			array('canonical_tag'=>"http://".$cache_uri)
		);
			
		$design->setPath( $config->getTemplatePath('index_page') );
		$design->display_cache('mp3_morecontent', $content, true, 3600*24*3);
		
		exit(0);
	} else {

		$content = array_merge(
			array('title_content'=>$video_title), 
			array('image_content'=>$video_image), 
			array('author_content'=>$video_author), 
			array('category_content'=>$video_category), 
			array('lenght_content'=>$video_duration), 
			array('views_content'=>$video_views), 
			array('raw_content'=>$video_content), 
			array('embedding_content'=>$video_embedding),
			array('title_html_de'=>$html_title),
			array('video_id'=>$video_id),
			array('video_url'=>$video_url),
			array('robots'=>"noindex,follow"),
			array('linking_text_content'=>$linking_texts),
			array('keyword_de'=>"$video_title, $html_title, $songtext_title"),
			array('description_de'=>$desc_title),
			array('songtext_artist'=>$songtext_artist),
			array('songtext_title'=>$songtext_title),
			array('songtext_content'=>$songtext_content),
			array('songtext_lang'=>$lang),
			array('duration'=>$duration),
			array('canonical_tag'=>"http://".$cache_uri)
		);
			
		$design->setPath( $config->getTemplatePath('index_page') );
		$design->display_cache('mp3_morecontent', $content, true, 3600*24*3);
	
		exit(1);
	}
}

?>