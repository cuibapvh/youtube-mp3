<?php

require_once( "connection.inc.php");
require_once( "config.inc.php" );
require_once('lang/Text/LanguageDetect.php'); 

class Functions {
	
function GetLanguageFromString($string){
	
	try{  
		$l = new Text_LanguageDetect();  
		$l->setNameMode(2); //return 2-letter language codes only  
		$result = $l->detect($string, 4);  
		return array_keys($result)[0];
		//print_r($result) ;
	}  
	catch (Text_LanguageDetect_Exception $e)   
	{     
	  
	}
	return "en";
}
	
function GetSongtextFromID($songtexts_id){

	$ResultArray	= array();
	
	$conn 			= new Connection();
	$config 		= new Config();

	$db 			= $config->lyrics_dir_database_songtextid();
	
	$songtexts_id	= mysql_real_escape_string($songtexts_id);
	$SqlQuery 		= "SELECT songtext FROM $db WHERE id='$songtexts_id' LIMIT 1;";
	
	$conn->db( $db );
	$MySqlArray = $conn->doSQLQuery( $SqlQuery );
	
	while( $sql_results = mysql_fetch_array($MySqlArray)) {	
		$songtext 	= $sql_results['songtext'];
		//echo "songtext=$songtext"; 
	}
	return $songtext;
}

function GetSongtextContent($letter,$shortid){

	$ResultArray		= array();
	
	$conn 				= new Connection();
	$config 			= new Config();

	$db 				= $config->lyrics_dir_database();
	$tbl 				= strtolower($letter);
	$shortid			= mysql_real_escape_string($shortid);
	
	$SqlQuery 			= "SELECT * FROM $tbl WHERE `shortid`='$shortid' LIMIT 1;";
	$conn->db( $db );
	$MySqlArray 		= $conn->doSQLQuery( $SqlQuery );
	
	while( $sql_results = mysql_fetch_array($MySqlArray)) {	
		$lang 			= $sql_results['lang'];
		$title 			= $sql_results['title'];
		$artist 		= $sql_results['artist'];
		$songtexts_id 	= $sql_results['songtexts_id'];
		$songtext		= $this->GetSongtextFromID($songtexts_id);
		array_push($ResultArray, "$title#####$artist#####$songtext#####$lang\n");
	}
	return $ResultArray;
}

function clearUTF($str)
{
   return preg_replace('/[^A-Za-z0-9\. -&;,<>]/', '', $str);
}


function clearUTFLong($s)
{
    $r = '';
    $s1 = iconv('UTF-8', 'ASCII//TRANSLIT', $s);
    for ($i = 0; $i < strlen($s1); $i++)
    {
        $ch1 = $s1[$i];
        $ch2 = mb_substr($s, $i, 1);

        $r .= $ch1=='?'?$ch2:$ch1;
    }
    return $r;
}

function GetLetterContent($letter,$Page){

	$ResultArray	= array();
	
	$conn 			= new Connection();
	$config 		= new Config();
	
	$db 			= $config->lyrics_dir_database();
	$tbl 			= mysql_real_escape_string(strtolower($letter));
	$max 			= $config->lyrics_dir_results_per_page();
	
	if ( $Page == 0 ) {					# limit 0, 30;
		$from = 0;			
		$SqlQuery = "SELECT * FROM $tbl ORDER BY id DESC LIMIT $max";
	} elseif ( $Page == 1 ){			# limit 31|62|93|122||,30
		$from = ( $max + $Page ); 
		$SqlQuery = "SELECT * FROM $tbl ORDER BY id DESC LIMIT $from, $max";
	} elseif ( $Page > 1 ){				# limit 31|62|93|122||,30
		$from = ( ( $Page * $max ) + $Page ); 
		$SqlQuery = "SELECT * FROM $tbl ORDER BY id DESC LIMIT $from, $max";
	} else {
		$SqlQuery = "SELECT * FROM $tbl ORDER BY id DESC LIMIT $max";
	};
	
	$conn->db( $db );
	$MySqlArray = $conn->doSQLQuery( $SqlQuery );
	$content .=<<<PAGES
		<ul>
PAGES;

	if ( $MySqlArray ) {
  
		while( $sql_results = mysql_fetch_array($MySqlArray)) {	
			$shortid 	= $sql_results['shortid'];
			$lang 		= $sql_results['lang'];
			$title 		= $sql_results['title'];
			$artist 	= $sql_results['artist'];
			$title 		= $this->clearUTF($title);
			$artist 	= $this->clearUTF($artist);
			/*
			$content .=<<<PAGES
<div class="id_result"><li><b><a href="/lyrics/songtexte.php?l=$letter&w=$shortid" class="url" itemprop="url" title="Lyrics von $artist - $title als Songtext anzeigen - Sprache: $lang" hreflang="$lang" rel="prefetch">Artist $artist mit Songtext $title: Sprache $lang</a></b></li></div>
PAGES;
			*/
			$content .=<<<PAGES
<div class="id_result"><li><b><a href="/lyrics/songtexte.php?l=$letter&w=$shortid" class="url" itemprop="url" title="Lyrics von $artist - $title als Songtext anzeigen - Sprache: $lang" hreflang="$lang">$artist - $title: $lang</a></b></li></div>
PAGES;
		}; // while( $sql_results = mysql_fetch_array($results)) { }

		//array_push($ResultArray, $content);
	} else {
	
		echo "Konnte die MySQL-Abfrage nicht verarbeiten / Could execute mysql query <br />\n";
		echo "MySQL-Antwort: " . mysql_error($conn);
		die();

	};  # if ( $MySqlArray ) {
	
	$content .=<<<PAGES
		</ul><br /><br />
PAGES;
	return $content;

}

function GetLettersCount($letters){

	$config 		= new Config();
	$conn 			= new Connection();
	$conn->db( $config->lyrics_dir_database() );
	$max_results	= $config->lyrics_dir_results_per_page();
	$letters 		= mysql_real_escape_string(strtolower($letters));
	
	if ( strlen($letters) == 2 ){
		$SqlQuery = "SELECT count(*) as total_count FROM $letters LIMIT 1;";
		$MySqlArray = $conn->doSQLQuery($SqlQuery);
		$row = mysql_fetch_object($MySqlArray);
		$row_count = $row->total_count;
	} else {
		$row_count = 0;
	}
	
	if($row_count > 0) {
		return ceil($row_count/$max_results);
	} else {
		return 0;
	}
}

function getDuration($title){

	$minute 		= rand(2,6);
	$seconds 		= rand(10,60);
	$code 			= "PT".$minute."M".$seconds."S";
	$linking_texts 	= <<< EOT
    <p>
	Album: <span itemprop="inAlbum" class="album">$title</span><br />
	Dauer: <span itemprop="duration" content="$code">$minute:$seconds</span>
	</p>
EOT;
	return $linking_texts;
}

function getDurationEN($title){

	$minute = rand(2,6);
	$seconds = rand(10,60);
	$code 		= "PT".$minute."M".$seconds."S";
	$linking_texts .= <<< EOT
    Album: <span itemprop="inAlbum" class="album">$title</span><br />
	Duration: <span itemprop="duration" content="$code">$minute:$seconds</span>
EOT;
	return $linking_texts;
}

function containsString($yt_title,$sphinx_hit){
	
	$bad_words = explode(" ", $yt_title);
	$valid = false;
	
	foreach ($bad_words as $bad_word) {
		if (stripos($sphinx_hit, $bad_word) !== false) {
			$valid = true;
			break;
		}
	}

	if ($valid) {
		return 1;
	} else {
		return 0;
	}

} // function containsString($yt_title,$sphinx_hit){

function FilterBadCharsForUrl($string, $full_url = true)
{
	$string = preg_replace("/[,_-]/", " ", $string); // replace spaces // don't replace . here
	$string = str_replace("%20", " ", $string); // replace html spaces
	if ($full_url) // don't replace ? = / & here
		$string = preg_replace("/[^a-z0-9 ÄäÖöÜüßÁáÀàÂâÉéÈèÊêÍíÌìÎîÓóÒòÔôÚúÙùÛûÇçæ?\/.=&]+/i", " ", $string);  // remove special chars
	else
		$string = preg_replace("/[^a-z0-9 ÄäÖöÜüßÁáÀàÂâÉéÈèÊêÍíÌìÎîÓóÒòÔôÚúÙùÛûÇçæ]+/i", " ", $string);
	$string = preg_replace("/[ ]+/", " ", $string); // remove double-spaces
	$string = preg_replace("/=[ ]+/", "=", $string); // remove space after GET-parameter
	$string = trim($string);
	return $string;
}
/*
	Remove Bad charcters from String
*/
function secString($del_badchar, $maxlength, $full_url = true) {
	
	if (!is_numeric($maxlength)){
		$maxlength = 5096;
	}

	$del_badchar 	= preg_replace("/[^a-z0-9\s+-]/i", " ", $del_badchar);
	
	$code_entities_match	= array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','~','`','=','^');
	$code_entities_replace	= array(' ',' ','','','','','','','','','','','','','','','','','','','','','','','');
	$del_badchar	= str_replace($code_entities_match, $code_entities_replace, $del_badchar);

	$del_badchar	= addslashes($del_badchar);
	$del_badchar	= strip_tags($del_badchar);
	$del_badchar	= escapeshellcmd($del_badchar);

	$del_badchar 	= preg_replace("/[,_-]/", " ", $del_badchar); // replace spaces // don't replace . here
	$del_badchar 	= str_replace("%20", " ", $del_badchar); // replace html spaces
	if ($full_url){ // don't replace ? = / & here
		$del_badchar = preg_replace("/[^a-z0-9 ÄäÖöÜüßÁáÀàÂâÉéÈèÊêÍíÌìÎîÓóÒòÔôÚúÙùÛûÇçæ?\/.=&]+/i", " ", $del_badchar);  // remove special chars
	} else {
		$del_badchar = preg_replace("/[^a-z0-9 ÄäÖöÜüßÁáÀàÂâÉéÈèÊêÍíÌìÎîÓóÒòÔôÚúÙùÛûÇçæ]+/i", " ", $del_badchar);
	}
	$del_badchar 	= preg_replace("/[ ]+/", " ", $del_badchar); // remove double-spaces
	$del_badchar 	= preg_replace("/=[ ]+/", "=", $del_badchar); // remove space after GET-parameter
	$del_badchar 	= trim($del_badchar);
	
	if ( strlen($del_badchar) >= $maxlength ) {
		# l?sche alles nach dem 200sten zeichen bei ?berlangen eingaben
		$del_badchar = substr($del_badchar, 0, $maxlength);		
	};
	
	// $itemId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
	return $del_badchar;
	
} # function secureString($del_badchar) {

/*
	Remove Bad charcters from String
*/
function secureString($del_badchar) {

	// later: give max string length and then remove this and give only $allowed_string_lenght zurück
	
	if ( strlen($del_badchar) > 75000 ) {
		# l?sche alles nach dem 200sten zeichen bei ?berlangen eingaben
		$del_badchar = substr($del_badchar, 0, 75000);		
	};

	$del_badchar 			= preg_replace("/[^a-z0-9\s+-]/i", " ", $del_badchar);
	
	$code_entities_match	= array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','~','`','=','^');
	$code_entities_replace	= array(' ',' ','','','','','','','','','','','','','','','','','','','','','','','');
	$del_badchar			= str_replace($code_entities_match, $code_entities_replace, $del_badchar);
	$del_badchar			= str_replace("'", '"', $del_badchar);
	
	$del_badchar			= addslashes($del_badchar);
	$del_badchar			= strip_tags($del_badchar);
	$del_badchar			= escapeshellcmd($del_badchar);

	return $del_badchar;
	
} # function secureString($del_badchar) {

function preapreXmlResults( $writer, $ResultsArray, $my_typ, $query_servers_enabled){

$config 	 	= new Config();
$server 		= $config->host_ip();
$wwwroot		= $config->wwwroot();
$previewtime	= $config->previewtime();

if ( $query_servers_enabled == 1 ){
	$server 		= $config->query_hosts_ip();
	$wwwroot		= $config->query_hosts_wwwoot();
}

$count 	= count($ResultsArray); 

	for ( $ArrayCount=0; $ArrayCount <= $count - 1; $ArrayCount++ ) {
				
		//array_push($ResultArray, "$fn#####$size#####$path#####$type\n");
		list( $f,$s,$p,$t )	= explode('#####', $ResultsArray[$ArrayCount] );
		//echo "$f,$s,$p,$t<br />";
		
		$res 		= str_replace($wwwroot,"http://$server/",$p);
		$https_res 	= str_replace($wwwroot,"https://$server/",$p);

		$res 		= str_replace(" ","%20",$res);
		$res 		= str_replace("&amp","&",$res);
		
		$https_res 	= str_replace(" ","%20",$https_res);
		$https_res 	= str_replace("&amp","&",$https_res);
			
		$preview 	= $res."?start=0&end=$previewtime";
		$preview_https 	= $https_res."?start=0&end=$previewtime";
		
		$f = trim($f);
		$s = trim($s);
		$p = trim($p);
		$t = trim($t);
		$f = preg_replace('/[^(\x20-\x7F)]*/','', $f);
		$f = preg_replace('/\\W/', ' ', $f);   
		
		$writer->startElement("item");
			$writer->writeElement("typ", utf8_encode($t));
			$writer->writeElement("name", utf8_encode($f));
			$writer->writeElement("preview", utf8_encode($preview));
			$writer->writeElement("securepreview", utf8_encode($preview_https));
			$writer->writeElement("link", utf8_encode($res));
			$writer->writeElement("securelink", utf8_encode($https_res));
			$writer->writeElement("size", utf8_encode($s));
			$writer->writeElement("category", utf8_encode($my_typ));
			$writer->writeElement("isdownloadable", 1 );
		$writer->endElement();
				
	} // for ( $ArrayCount=0; $ArrayCount<=$count - 1; $ArrayCount++	 ) {

	return $writer;
} // function preapreXmlResults( $writer, $ResultsArray, $type){


function array_unique_recusive($arr){
	foreach($arr as $key=>$value)
	if(gettype($value)=='array')
		$arr[$key]=array_unique_recusive($value);
	return array_unique($arr,SORT_REGULAR);
}


function curl_isAlive($Url){
 
    // is cURL installed yet?
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }
 
    // OK cool - then let's create a new cURL resource handle
    $ch = curl_init();
 
    // Now set some options (most are optional)
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);
 
    // Set a referer
    curl_setopt($ch, CURLOPT_REFERER, "http://www.buzzerstar.com/");
 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch,CURLOPT_SSLVERSION,3);
	
    // User agent
    curl_setopt($ch, CURLOPT_USERAGENT, "BuzzerStar Mobile/1.0");
 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 1);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 2);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,2);
	
	curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1 );
	$page = curl_exec($ch);
	
	// get HTTP response code
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode == 200)
        return 1;
    else
        return 0;
}


function curl_get($Url){
 
    // is cURL installed yet?
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }
 
    // OK cool - then let's create a new cURL resource handle
    $ch = curl_init();
 
    // Now set some options (most are optional)
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);
 
    // Set a referer
    curl_setopt($ch, CURLOPT_REFERER, "http://www.buzzerstar.com/");
 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	
    // User agent
    curl_setopt($ch, CURLOPT_USERAGENT, "BuzzerStar Mobile/1.0");
 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
 
    if ($httpcode == 200)
        return $output;
    else
        return 0;

}

function autolink($str, $attributes=array()) {
	$attrs = '';
	foreach ($attributes as $attribute => $value) {
		$attrs .= " {$attribute}=\"{$value}\"";
	}

	$str = ' ' . $str;
	$str = preg_replace(
		'`([^"=\'>])((http|https|ftp)://[^\s<]+[^\s<\.)])`i',
		'$1<a href="$2"'.$attrs.'>$2</a>',
		$str
	);
	$str = substr($str, 1);
	
	return $str;
}

function getip(){
	if($_SERVER["HTTP_X_FORWARDED_FOR"] != ""){
	   $IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
	   $proxy = $_SERVER["REMOTE_ADDR"];
	 //  $host = @gethostbyaddr($_SERVER["HTTP_X_FORWARDED_FOR"]);
	}else{
	   $IP = $_SERVER["REMOTE_ADDR"];
	   $proxy = "No proxy detected";
	  // $host = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);
	}
	return $IP;
}

}
?>