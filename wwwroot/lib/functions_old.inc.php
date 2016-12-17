<?php
class Functions {

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