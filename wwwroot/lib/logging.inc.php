<?php

class Logging {

#querys from user to be logged
function logQuerys( $SearchQuery ) {

	//$isvalid = is_good($SearchQuery); # security.inc.php
	//echo "($isvalid) on query: '$query' <br>";
//	if ( $isvalid != 1 ) { 
//		return 0;
//	};

	$KeywordDate		= date("j.n.Y");
	$KeyWordStoreDir	= date("n.Y");

	# setze den storepath -> jeden tag neu
	//$StorePath	= LOGLINKSSTORE."\\".$KeyWordStoreDir; // win version
	$StorePath	= "/home/log/".$KeyWordStoreDir; // linux version
	
	# erstelle verzeichnis, wenn es nicht existiert
	if (!is_dir ( $StorePath ) ) {
		mkdir ($StorePath, 0777);
	};

	# erstelle absoluten pfad zur keyword store datei
	$StorePathFile = $StorePath . "/$KeywordDate.songtextsearch.txt";
	
	$neu = trim(str_replace("+"," ",$SearchQuery));
	$fh  = fopen("$StorePathFile","a+");
		flock($fh, LOCK_EX);
		fputs($fh,"$neu\n");
	fclose($fh);

	// pornrage.com keyword to twitter.com poster: social.inc.php
	//$shorturl = makeShortUrl($SearchQuery);
	//postTwitter($shorturl,$SearchQuery);
	
	return 1;

} # function logQuerys( $SearchQuery, $catg ) {

}
?>