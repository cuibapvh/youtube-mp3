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

require_once( "lib/SSDTube.php" ); 
require_once( "lib/config.inc.php" );
require_once( "lib/connection.inc.php");
require_once( "lib/youtube/yt_functions.inc.php");

$config 		= new Config();
$conn 			= new Connection();
$objSSDTube 	= new SSDTube();

//$conn->db( $config->sql_dbname() );
//$table 			= $config->sql_tablename();

$video 			= urldecode($_REQUEST['video']);

if (!preg_match("/^((https?:\/\/)?(w{0,3}\.)?youtu(\.be|(be|be-nocookie)\.\w{2,3}\/))((watch\?v=|v|embed)?[\/]?(?P<video>[a-zA-Z0-9-_]{11}))/si", $video)){
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: http://www.youtube-mp3.mobi/"); 
	exit(1);
}

$objSSDTube->identify($video, true);
$my_id 			= parseVideoID($video);
$downloadLk 	= getDownloadLink($my_id);
$downloadTitle 	= str_replace(" ", ".", $objSSDTube->title);
$folder			= $config->download_store_temp_path();
$finalFile		= $folder."/".$downloadTitle.".mp4";

//echo "DEBUG: Downloading: $downloadTitle<br />";
// download file via ipv6
curlGetStore($downloadLk, $finalFile );

$ffmpeg_bin 	= $config->ffmpeg_bin();
$store_path		= $config->mp3_store_path();
$mp3_id			= md5(time().$video.$finalFile.$downloadTitle);
$escapeFile 	= escapeshellcmd($finalFile);
//$t 				= system("$ffmpeg_bin -i $escapeFile -acodec libmp3lame -ab 192k $store_path/$mp3_id.mp3", $retVal); 
$t 				= system("$ffmpeg_bin -i $escapeFile -f mp3 -ab 320000 -ac 2 -ar 44100 -vn $store_path/$mp3_id.mp3", $retVal); 
//$t 				= system("mplayer -vc dummy -vo null -ao pcm:file=$store_path/$mp3_id.wav $escapeFile"); // apt-get install mplayer
//$t 				= system("lame -h -b320 $store_path/$mp3_id.wav $store_path/$mp3_id.mp3"); // apt-get install lame
//echo "$ffmpeg_bin -i $escapeFile -f mp3 -ab 320000 -ac 2 -ar 44100 -vn $store_path/$mp3_id.mp3";

//echo "t=$t und retval=$retVal<br />";
if ($retVal != 0) {
	$error = "E101: Error Converting To MP3: Video Download not allowed from this Server.";
	$query_update = "UPDATE converter SET mp3_ready = '0', mp3_error = '$error' WHERE video_url = '$video';";
} else {
	$query_update = "UPDATE converter SET mp3_ready = '1', mp3_id = '$mp3_id' WHERE video_url = '$video';";
}

//echo "query_update=$query_update";
$conn = new Connection();
$conn->db( $config->sql_dbname() );
$conn->doSQLQuery( $query_update );

exit(0);
?>