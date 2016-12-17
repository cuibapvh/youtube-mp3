<?php

// Install: pear install Net_DNS
class IP {


function isValidGoogleCrawler(){
	
	if ( isGooglebotUA() === TRUE || isGoogleCrawlerUA() === TRUE ){ // this is the correct entry for the check
		$crawlerIP 	= defaultGetIP();
		$reverse 	= reverseLookup($crawlerIP);
		$isValid 	= matchBots("$crawlerIP", $reverse);
		
		return $isValid; // 0=fake googel bot // 1=true google bot
	}
	return -1; // intern error
}


function matchBots($orgIP, $reverse){
	$reverse_host = str_replace(".","-", $orgIP);
				
	$babot = "baiduspider-$reverse_host.crawl.baidu.com";
	$gbot = "crawl-$reverse_host.googlebot.com";
	$yabot = ".crawl.yahoo\.com";
	$yanbot = "spider-$reverse_host.yandex.com";
	$bibot = "msnbot-$reverse_host.search.msn.com";
	
	//echo "matchBots( $gbot)<br />";
	if ( strcasecmp($reverse, $gbot ) == 0 ) {
	#	print "(VALID): $host is offical bot\n";
		return 1;
	} else {
	#	print "(ERROR): $host is NOT offical bot\n";
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
	return $IP;
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