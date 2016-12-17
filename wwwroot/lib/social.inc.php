<?php

require_once ("/server/wwwroot/www.pornrage.com/lib/functions.inc.php");
require_once ("/server/wwwroot/www.pornrage.com/lib/twitter.inc.php");

function makeShortUrl($SearchQuery){

	// bit.ly api parameters
	$username 		= "pornrage";	
	$password		= "Se7C+!C00L@win";
	$bitly_api		= "R_d35b4a15e9854fa3c5076bfd81e10f01";

	$longurl		= "http://www.pornrage.com/videos.php?s=" .strtolower(title_plus($SearchQuery));
	$longurl		= urlencode($longurl);
	
	/*
	$rand			= rand(1,2);
	
	if ( $rand	== 1 ){
		$myshorturl = isgd($longurl);
		//echo "isgd '$myshorturl' ";
	} elseif ( $rand	== 2 ){
		$myshorturl = tinyUrl($longurl);
		//echo "tinyurl '$myshorturl' ";
	};
		
	if ( strlen($myshorturl) >= 13 ){
		return $myshorturl;
	};
	*/
	
	$bitlyurl		= "http://api.bit.ly/shorten?version=2.0.1&longUrl=$longurl&login=$username&apiKey=$bitly_api";
	$returnContent	= file_get_contents($bitlyurl);
	$Array			= explode('"', $returnContent);
	$ShortUrl		= $Array[25];

	return $ShortUrl;

}; #function makeShortUrl(){


function postTwitter($url,$search){

	$titter_user 	= "pornrage";
	$titter_pass 	= "gdf54546zddjg!1+";
	$tweet 			= new Twitter($titter_user, $titter_pass);
	/*
	if ( rand(1,2) == 1 ){
		//$text = "$url - $search Free Porn Videos and Sex Movie Tube with XXX Streams";
	} else  {
		//$text = "$url - $search Free Porn Videos,Sex Tube Movies,XXX Sex Videos,Teen Pussys";
	};
	*/
	
	if ( strlen($url) < 7 ){
		$url = "http://www.pornrage.com/videos.php?s=" .strtolower(title_plus($search));
	};
	
	$text = "$url - #porntube #sextube for $search";
	if ( strlen($text) > 140 ) {
		$text = substr($text, 0, 139);		
	};
	$success 		= $tweet->update($text);

	return 1;

}; # function postTwitter($url,$search){ 


function tinyUrl($url) {
	$returnContent	= file_get_contents('http://tinyurl.com/api-create.php?url='.urlencode($url));
	//echo 'http://tinyurl.com/api-create.php?url='.urlencode($url);
	return $returnContent;
}
	
function isgd($url) {
	$returnContent	= file_get_contents('http://is.gd/api.php?longurl='.urlencode($url));
	return $returnContent;
}

	
?>