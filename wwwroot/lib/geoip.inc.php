<?php

// Install: pear install Net_DNS
//require_once("geoip/src/geoip.inc");
// install: http://www.php.net/manual/en/geoip.setup.php

require_once("ip.inc.php");

class GeoIPClass {

function getCountryCode(){


//	$gi 		= geoip_open("/home/wwwyoutube/lib/geoip/data/GeoIP.dat", GEOIP_STANDARD);
	
	$ipClass	= new IP();
	$ip 		= $ipClass->defaultGetIP();

	//$code 		= geoip_country_code_by_addr($gi, $ip);
	//geoip_close($gi);
	return geoip_country_code_by_name ($ip);

}

}
?>