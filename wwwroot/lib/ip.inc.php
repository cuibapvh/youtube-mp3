<?php

/*
####
## Topic: Block fake googlebots from accessing your valid high quality content
## Version: 1.4 / 26.4.2014
## Author: Sebastian Enger
## Check out: http://www.youtube-mp3.mobi/
## eMail: sebastian.enger@gmail.com
## Questions?, Want new Features? Mail me: sebastian.enger@gmail.com
## Licence: GPL 2 
## Done: cache offical ips and check incoming request against this database, to prevent too much dns requests
####
*/

// Install: pear install Net_DNS
class IP {

function isValidGoogleCrawler( $storelist = "/tmp/iosec_botlog.txt" ){
	
	if ( isGooglebotUA() === TRUE || isGoogleCrawlerUA() === TRUE ){ // this is the correct entry for the check
		$crawlerIP 	= defaultGetIP();
		$trimmed 	= file($storelist, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		/*
		If we find the current bot ip in our offical crawler ip flatfile $storelist 
		then simply validate this as a valid crawler
		*/
		if (array_key_exists($crawlerIP, $trimmed)) {
			return 1;
		}
		
		$reverse 	= reverseLookup($crawlerIP);
		$isValid 	= matchGoogleBots($crawlerIP, $reverse);
		
		if ( $isValid == 1 ){
			file_put_contents($storelist, "$crawlerIP\n", FILE_APPEND | LOCK_EX);
		}
		
		return $isValid; // 0=fake google bot // 1=true google bot
	}
	return -1; // intern error
}

function isValidCrawler( $storelist = "/tmp/iosec_botlog.txt" ){
	
	$crawlerIP 	= defaultGetIP();
	$trimmed 	= file($storelist, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	/*
	If we find the current bot ip in our offical crawler ip flatfile $storelist 
	then simply validate this as a valid crawler
	*/
	if (array_key_exists($crawlerIP, $trimmed)) {
		return 1;
	}
		
	$reverse 	= reverseLookup($crawlerIP);
	$isValid 	= matchBotsAll($crawlerIP, $reverse);
	
	if ( $isValid == 1 ){
		file_put_contents($storelist, "$crawlerIP\n" , FILE_APPEND | LOCK_EX);
	}
		
	return $isValid; // 0=fake bot // 1=true valid offical bot
}

function matchBotsAll($orgIP, $reverse){
	
	$reverse_host = str_replace(".","-", $orgIP);
				
	$babot = "baiduspider-$reverse_host\.crawl\.baidu\.com";
	$gbot = "crawl-$reverse_host\.googlebot\.com";
	$yabot = "\.crawl\.yahoo\.com";
	$yanbot = "spider-$reverse_host\.yandex\.com";
	$bibot = "msnbot-$reverse_host\.search.msn\.com";
	$alexbot = "$reverse_host\.compute-1\.amazonaws\.com";
	
	//echo "matchBots( $gbot)<br />";
	if ( preg_match("/$babot/i", $reverse ) || preg_match("/$gbot/i", $reverse ) || preg_match("/$yabot/i", $reverse ) || preg_match("/$yanbot/i", $reverse ) || preg_match("/$bibot/i", $reverse ) || preg_match("/$alexbot/i", $reverse ) ) {
	#	print "(VALID): $host is offical bot\n";
		return 1;
	} else {
	#	print "(ERROR): $host is NOT offical bot\n";
		return 0;
	}
	
}

function matchGoogleBots($orgIP, $reverse){
	
	$reverse_host = str_replace(".","-", $orgIP);
				
	$babot = "baiduspider-$reverse_host\.crawl\.baidu\.com";
	$gbot = "crawl-$reverse_host\.googlebot\.com";
	$yabot = "\.crawl\.yahoo\.com";
	$yanbot = "spider-$reverse_host\.yandex\.com";
	$bibot = "msnbot-$reverse_host\.search.msn\.com";
	$alexbot = "$reverse_host\.compute-1\.amazonaws\.com";
	
	//echo "matchBots( $gbot)<br />";
	if ( preg_match("/$gbot/i", $reverse ) ) {
	#	print "(VALID): $host is offical googlebot\n";
		return 1;
	} else {
	#	print "(ERROR): $host is NOT offical googlebot\n";
		return 0;
	}
	
}

function defaultGetIP(){
	$IP = 0;
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		if(is_array($IP) && isset($IP[0])){ $IP = $IP[0]; } //It seems that some hosts may modify _SERVER vars into arrays.
	}
	if((! preg_match('/(\d+)\.(\d+)\.(\d+)\.(\d+)/', $IP)) && isset($_SERVER['HTTP_X_REAL_IP'])){
		$IP = $_SERVER['HTTP_X_REAL_IP'];
		if(is_array($IP) && isset($IP[0])){ $IP = $IP[0]; } //It seems that some hosts may modify _SERVER vars into arrays.
	}
	if((! preg_match('/(\d+)\.(\d+)\.(\d+)\.(\d+)/', $IP)) && isset($_SERVER['REMOTE_ADDR'])){
		$IP = $_SERVER['REMOTE_ADDR'];
		if(is_array($IP) && isset($IP[0])){ $IP = $IP[0]; } //It seems that some hosts may modify _SERVER vars into arrays.
	}
	return trim($IP);
}

function reverseLookup($IP){
	
	$ptr = implode(".", array_reverse(explode(".",$IP))) . ".in-addr.arpa";
	$host = @dns_get_record($ptr, DNS_PTR);
	if($host == null){
		$host = 'NONE';
	} else {
		$host = $host[0]['target'];
	}

	if($host == 'NONE'){
		return 'NONE';
	} else {
		return $host;
	}
}

	
function isValidIP($IP){
	if(preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $IP, $m)){
		if(
			$m[0] >= 0 && $m[0] <= 255 &&
			$m[1] >= 0 && $m[1] <= 255 &&
			$m[2] >= 0 && $m[2] <= 255 &&
			$m[3] >= 0 && $m[3] <= 255
		){
			return true;
		}
	}
	return false;
}

function getRequestedURL(){
	if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']){
		$host = $_SERVER['HTTP_HOST'];
	} else {
		$host = $_SERVER['SERVER_NAME'];
	}
	$prefix = 'http';
	if( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ){
		$prefix = 'https';
	}
	return $prefix . '://' . $host . $_SERVER['REQUEST_URI'];
}

function inet_ntoa($ip){
	$long = 4294967295 - ($ip - 1);
	return long2ip(-$long);
}
function inet_aton($ip){
	return sprintf("%u", ip2long($ip));
}

function isGooglebotUA(){
	$UA = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
	if(preg_match('/Googlebot\/\d\.\d/', $UA)){ // UA: Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html) or (rarely used): Googlebot/2.1 (+http://www.google.com/bot.html)
		return true;
	}
	return false;
}
function isGoogleCrawlerUA(){
	$UA = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
	$googPat = array(
'@^Mozilla/5\\.0 \\(.*Google Keyword Tool.*\\)$@',
'@^Mozilla/5\\.0 \\(.*Feedfetcher\\-Google.*\\)$@',
'@^Feedfetcher\\-Google\\-iGoogleGadgets.*$@',
'@^searchbot admin\\@google\\.com$@',
'@^Google\\-Site\\-Verification.*$@',
'@^Google OpenSocial agent.*$@',
'@^.*Googlebot\\-Mobile/2\\..*$@',
'@^AdsBot\\-Google\\-Mobile.*$@',
'@^google \\(.*Enterprise.*\\)$@',
'@^Mediapartners\\-Google.*$@',
'@^GoogleFriendConnect.*$@',
'@^googlebot\\-urlconsole$@',
'@^.*Google Web Preview.*$@',
'@^Feedfetcher\\-Google.*$@',
'@^AppEngine\\-Google.*$@',
'@^Googlebot\\-Video.*$@',
'@^Googlebot\\-Image.*$@',
'@^Google\\-Sitemaps.*$@',
'@^Googlebot/Test.*$@',
'@^Googlebot\\-News.*$@',
'@^.*Googlebot/2\\.1.*$@',
'@^.*Googlebot*$@',
'@^AdsBot\\-Google.*$@',
'@^Google$@'
);
	
	foreach($googPat as $pat){
		if(preg_match($pat . 'i', $UA)){
			return true;
		}
	}
	return false;
}

}
?>