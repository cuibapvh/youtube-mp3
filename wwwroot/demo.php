<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once( "lib/geoip.inc.php");
require_once( "lib/mobile/Mobile_Detect.php");

$detect 			= new Mobile_Detect;
$geoip	 			= new GeoIPClass();

$deviceType 		= ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
$deviceTypeMobile 	= $detect->isMobile();
$deviceTypeTablet 	= $detect->isTablet();
$countryCode		= $geoip->getCountryCode();	 

echo "deviceType=$deviceType<br>";
echo "deviceTypeMobile=$deviceTypeMobile<br>";
echo "deviceTypeTablet=$deviceTypeTablet<br>";
echo "countryCode=$countryCode<br>";
?>