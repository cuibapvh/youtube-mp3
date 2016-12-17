<?php
class Functions {


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


/*
	Remove Bad charcters from String
*/
function secureString($del_badchar) {

	if ( strlen($del_badchar) > 256 ) {
		# l?sche alles nach dem 200sten zeichen bei ?berlangen eingaben
		$del_badchar = substr($del_badchar, 0, 256);		
	};

	$del_badchar 			= preg_replace("/[^a-z0-9\s+-]/i", " ", $del_badchar);
	
	$code_entities_match	= array(' ','--','&quot;','!','@','#','$','%','^','&','*','(',')','_','{','}','|',':','"','<','>','?','[',']','\\',';',"'",',','.','/','*','~','`','=','^');
	$code_entities_replace	= array(' ',' ','','','','','','','','','','','','','','','','','','','','','','','');
	$del_badchar			= str_replace($code_entities_match, $code_entities_replace, $del_badchar);

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