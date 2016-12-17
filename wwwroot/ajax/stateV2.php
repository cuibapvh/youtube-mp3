<?php

require_once( "../lib/config.inc.php" );
require_once( "../lib/connection.inc.php");
require_once( "../lib/state.inc.php");
require_once( "../lib/functions.inc.php");
// http://www.nookiestar.com/mp3/ajax/state.php?language=DE&video_id={video_id}

$config 	= new Config();
$conn 		= new Connection();
$state 		= new State();
$function 	= new Functions();

$conn->db( $config->sql_dbname() );
$table 		= $config->sql_tablename();
$temp		= $config->ajax_state_temp_path();

$language	= $function->secureString(strtolower($_REQUEST['language']));
$video_id	= $function->secureString($_REQUEST['video_id']);
$mp3_download_uri = $config->mp3_download_uri();

// check if state ready: Start
if (file_exists("$temp/$video_id.$language")){
	echo file_get_contents("$temp/$video_id.$language");
	exit(0);
}

$SqlQuery 	= "SELECT video_title,mp3_id,mp3_ready,mp3_error FROM $table WHERE video_id = '$video_id' LIMIT 1;";
$MySqlArray = $conn->doSQLQuery( $SqlQuery );

if ( $MySqlArray ) {
	while( $sql_results = mysql_fetch_array($MySqlArray)) {
		$mp3_id 	= $sql_results["mp3_id"];
		$mp3_ready 	= $sql_results["mp3_ready"];
		$mp3_error 	= $sql_results["mp3_error"];		
		$mp3_title 	= $sql_results["video_title"];		
	}; # while( $sql_results = mysql_fetch_array($results)) { }
}

//echo "mp3_id=$mp3_id mp3_ready=$mp3_ready mp3_error=$mp3_error<br/>";
if ( $mp3_ready == "1" && strlen($mp3_id) == 32 && $mp3_error == "0" ){	
	$retVal = "<h4 style=\"font-size: 16px;\">". $state->stateHandler("ready",$language) . ' ---> <strong><b><a href="'.$mp3_download_uri.$mp3_id."&mp3_title=".$mp3_title.'" rel="nofollow">MP3 Download</a></b></strong></h4>';
	file_put_contents("$temp/$video_id.$language", $retVal, LOCK_EX);
	echo $retVal;
	//echo "Download here www.downloadcontent.com/mp3.download.php?dl";
} elseif ( $mp3_ready == "0" && $mp3_error == "0" && strlen($mp3_id) != 32){
	//echo "Video wird grad umgewandelt.";
	echo $state->stateHandler("working",$language);
} elseif ( $mp3_ready == "0" && strlen($mp3_error) > 5 && strlen($mp3_id) != 32){
	//echo "Es trat ein Fehler auf: $mp3_error";
	echo $state->stateHandler("error",$language) . $mp3_error;
} else {
	echo $state->stateHandler("else",$language);
}

exit(0);
?>