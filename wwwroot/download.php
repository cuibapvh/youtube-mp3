<?php

require_once( "lib/config.inc.php" );
require_once( "lib/search.inc.php" );
require_once( "lib/functions.inc.php" );

$config 	= new Config();
$search 	= new Search();
$function	= new Functions();

//error_reporting(E_ALL); // devel

$mp3 				= urldecode($_REQUEST['mp3']);
$video_title 		= urldecode($_REQUEST['mp3_title']);
$searchTitle 		= trim(preg_replace("/\([^)]+\)/","", $video_title)); // http://stackoverflow.com/questions/2174362/remove-text-between-parentheses-php
$searchTitle 		= preg_replace('/\W/',' ', $searchTitle);
$video_title 		= str_replace(" ", ".", $video_title);

$SongtextRawContent = $search->SphinxSearch( $function->secureString($searchTitle) );
list( $songtext_title,$songtext_artist,$songtext_content ) = explode('#####', $SongtextRawContent[0] );
$songtext_content 	= preg_replace('#(<br\ ?\/?>)+#i', "\n", $songtext_content); # 
$songtext_content 	= preg_replace('#(<br */?>\s*)+#i', "\n", $songtext_content); # 

$md5_file 			= md5($songtext_title.$songtext_artist.$songtext_content);
$lyric_file			= "/home/ajax/lyrics/$md5_file";
file_put_contents($lyric_file, $songtext_content);
$fullPath 			= $config->mp3_store_path()."/".$mp3.".mp3";

/*
//echo "searchTitle =$searchTitle <br /> songtext_content=$songtext_content<br />";
if ( strlen($songtext_content) > 30 && file_exists($fullPath)){
	//VERSION 1:shell_exec("/usr/local/bin/eyeD3 -2 --to-v2.4 -a \"$songtext_artist\" -t \"$songtext_title\" --lyrics=\"eng:$songtext_title:$songtext_content\" $fullPath 2>&1");
	
	shell_exec("/usr/local/bin/eyeD3 --remove-lyrics $fullPath");
	// source downloaded from: https://bitbucket.org/nicfit/eyed3 
	system("/usr/local/bin/eyeD3 -2 --to-v2.4 --add-comment=\"Legal geladen von www.youtube-mp3.mobi:Downloaded by www.youtube-mp3.mobi:eng\" --artist=\"$songtext_artist\" --title=\"$songtext_title\" --add-lyrics=$lyric_file:\"$songtext_title:eng\" $fullPath 2>&1");
}
*/

if ( file_exists($fullPath) ) {
    $fsize = filesize($fullPath);
	
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	header("Content-Transfer-Encoding: binary");
	header("Content-type: application/octet-stream");
	header("Content-Disposition: attachment; filename=\"".$video_title.".mp3"."\""); 
    header("Content-length: $fsize");
    
	ob_clean();
	flush();
	readfile($fullPath);
	exit(0);
}
exit(0);
// example: place this kind of link into the document where the file download is offered:
// <a href="download.php?download_file=some_file.pdf">Download here</a>
?>