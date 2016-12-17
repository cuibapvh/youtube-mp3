<?php

//error_reporting(E_ALL);
//error_reporting (E_ALL ^ E_NOTICE);
//error_reporting(0);

require_once ( "lib/config.inc.php" );
require_once ( "lib/connection.inc.php" );
require_once ( "lib/search.inc.php" );
require_once ( "lib/functions.inc.php" );

$config 	 	= new Config();
$conn 			= new Connection();
$func 			= new Functions();
$searchObj		= new Search();

$search 	 	= preg_replace('/\b$(["\)])/','\$ $1', $func->secureString($_REQUEST['q'])); 
$search			= str_replace("%20"," ",$search);

$search_catg 	= $config->config_search();
$max_results 	= $config->sql_max_results();

$writer = new XMLWriter();
$writer->openMemory();
$writer->setIndent(true);
$writer->startDocument("1.0", "UTF-8");
$writer->startElement("resultlist");

header("Content-Type: text/xml; charset=UTF-8");
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
	
//echo "DO NOT using query server";
$resultcount = 0;
foreach ($search_catg as $catg){
	
	$ResultsArray	= array();
	$ResultsArray 	= $searchObj->SphinxSearch($search, $catg);
	$resultcount 	+= count($ResultsArray); 
	$my_typ			= "searchresult";
	$writer 		= $func->preapreXmlResults($writer, $ResultsArray, $my_typ, 0 );	
//	echo strlen($search) . " Search: $search<br>" ;
}

//$max_random_results = ceil(($max_results - $resultcount)/count($search_catg));
$max_random_results = $config->max_random_results();

foreach ($search_catg as $catg){
	
	$ResultsArray	= array();
	$ResultsArray 	= $searchObj->RandomResults( $catg, $max_random_results );
	$count 			= count($ResultsArray); 
	$my_typ			= "randomresult";
	$writer 		= $func->preapreXmlResults($writer, $ResultsArray, $my_typ, 0 );
} // foreach ($search_catg as $catg){

$writer->endElement();
$writer->endDocument();
echo $writer->outputMemory(true);
//echo "</resultlist>";
exit(0);

?>