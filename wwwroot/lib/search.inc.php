<?php

// /etc/init.d/sphinxsearch start
require_once ( "config.inc.php" );
require_once ( "connection.inc.php" );
require_once ( "sphinxapi.inc.php" );
require_once ( "functions.inc.php" );

class Search {

	function ytSearch($search, $Page){
		
		$ResultArray	= array();
		
		$config 	= new Config();
		$function 	= new Functions();

		$count		= $config->yt_result_count();
		$max 		= $config->yt_result_count_max();
		
		if ( $Page == 0 ) {					# limit 0, 30;
			$from = 1;			
		} elseif ( $Page == 1 ){			# limit 31|62|93|122||,30
			$from = ( $max + $Page ); 
		} elseif ( $Page > 1 ){				# limit 31|62|93|122||,30
			$from = ( ( $Page * $max ) + $Page ); 
		} else {
			$from = 1;		
		};

		$orderby		= "relevance";
		$search 		= urlencode($search);
		$orderby 		= urlencode($orderby);
		
		//$xmlUri 		= 'http://gdata.youtube.com/feeds/api/videos?q='.$search.'&orderby='.$orderby.'&start-index='.$from.'&max-results='.$count.'&v=2&restriction=DE&format=5&alt=json';
		
		$xmlUri 		= 'http://gdata.youtube.com/feeds/api/videos?q='.$search.'&orderby='.$orderby.'&start-index='.$from.'&max-results='.$count.'&v=2&format=5&alt=json';
		
		$ytXmlContent 	= $function->curl_get($xmlUri);
		$decode 		= json_decode($ytXmlContent, TRUE);
		
		/*
		foreach ($decode['feed']['entry'] as $entry) {
			echo '<a href="' . $entry['link'][0]['href'] . '">' . $entry['title']['$t'] . '</a>';
			echo " by ".  $entry['author'][0]['name']['$t'];
			echo "<br />";
		}
		*/
		foreach ($decode['feed']['entry'] as $entry) {
			$ytLink = trim($entry['link'][0]['href']);
			$ytTitle = trim($entry['title']['$t']);
			$ytAuthor = trim($entry['author'][0]['name']['$t']);
			if (strstr($ytLink, '&')==0){
				$list = explode('&', $ytLink);
				$ytLink = reset($list);
			}
			//echo "ytLink: $ytLink<br />";
			array_push($ResultArray, "$ytTitle#####$ytLink#####$ytAuthor\n");
		}
		return $ResultArray;
	}  

	function SphinxSearch( $SearchQuery ){

		if ( !preg_match('/([\w+]|[\d+]|[^A-Za-z0-9]|[a-z]|[0-9]{2,})/i', $SearchQuery) || strlen($SearchQuery) <= 1 ){
		//	echo "SphinxSearch searchquery is null: $SearchQuery<br />";
			$ResultArray	= array();
			return $ResultArray;
		};
	
		$config 	= new Config();
		$maxresults = $config->sql_max_sphinx_mp3_integration_results();
		$db 		= $config->sql_sphinx_dbname();
		$conn 		= new Connection( );
		$conn->db( $db );

		$tbl 		= $config->sql_sphinx_table();
		$index		= $config->sphinx_index();
		
		$cl 		= new SphinxClient();
		$q 			= $SearchQuery;
		$sql 		= "";
		$host 		= "127.0.0.1";
		$port 		= 9312;
		$groupby 	= "";
		$groupsort 	= "@group desc";
		$filter 	= "group_id";
		$filtervals = array();
		$distinct 	= "";
		$sortby 	= "";
		$limit 		= $maxresults;
		$ranker 	= 'SPH_RANK_PROXIMITY_BM25';
		$select 	= "";
		$docids 	= array();

		//$mode = "SPH_MATCH_FULLSCAN";
		//$mode = "SPH_MATCH_ANY";
		//$mode = "SPH_MATCH_BOOLEAN";
		//$mode = "SPH_MATCH_EXTENDED";
		$mode = 'SPH_MATCH_EXTENDED2';
		//$mode = "SPH_MATCH_PHRASE";

		//echo "good";
		////////////
		// do query
		////////////127.0.0.1:9312
		$cl->SetServer("/var/run/searchd.sock", 0);
		//$cl->SetServer ( $host, $port );
		$cl->SetConnectTimeout ( 3 );
		$cl->SetArrayResult ( true );
		$cl->SetMatchMode ( $mode );
		$cl->SetFieldWeights ( array ( "title"=>100,"artist"=>65,"songtext"=>15, ) );
		$cl->ResetFilters();
		//$cl->SetSortMode( SPH_SORT_RELEVANCE );
		$cl->SetSortMode('SPH_SORT_RELEVANCE', "title");
		$cl->SetRankingMode( $ranker );
		$cl->SetGroupDistinct ( $distinct );

		//if ( $sortby )				$cl->SetSortMode ( SPH_SORT_EXTENDED, $sortby );
		//if ( $sortexpr )			$cl->SetSortMode ( SPH_SORT_EXPR, $sortexpr );

		$cl->SetSelect ( $select );
		$cl->SetLimits ( 0, $maxresults );
		//echo "good 2";
		$res = $cl->Query ( $q, $index );
		//echo "good 3";

		if ( $res===false )
		{
		//	echo "Query failed: " . $cl->GetLastError() . ".\n";

		} else {
			if ( $cl->GetLastWarning() ) {
			//	echo "WARNING: " . $cl->GetLastWarning() . "\n\n";
			};
			//echo count($res["matches"]); 
			//echo "<br>";
			
			if ( is_array($res["matches"]) )
			{
				$n = 1;
				foreach ( $res["matches"] as $docinfo )
				{
				//	echo "$n. doc_id=$docinfo[id], weight=$docinfo[weight]<br />";
					array_push($docids, $docinfo["id"]);

				}
			}
		}
		
	//	print_r($docids);

		$ResultArray	= array();

		if (count($docids) <= 0 ){
			return $ResultArray; // gib leeres array zurück
		};

		$SqlQuery	= "SELECT * FROM $tbl WHERE";
			for ($i=0;$i<=count($docids) - 1;$i++){
				if ($i>0){
					$SqlQuery .= "\"$docids[$i]\",";
				} elseif($i<=0){
					$SqlQuery .= " `id` IN (\"$docids[$i]\",";
				};
			};
			$SqlQuery = substr($SqlQuery,0,(strlen($SqlQuery)-1));
			$SqlQuery .=");";
			
		$MySqlArray 	= $conn->doSQLQuery( $SqlQuery );

		if ( $MySqlArray ) {
			while( $sql_results = mysql_fetch_array($MySqlArray)) {
				$fn		= $sql_results['title'];
				$path 	= $sql_results['artist'];
				$type 	= $sql_results['songtext'];
				
				$type 	= preg_replace('#(<br\ ?\/?>)+#i', '<br />', $type); # 
				$type 	= preg_replace('#(<br */?>\s*)+#i', '<br />', $type); # http://stackoverflow.com/questions/7738439/how-to-regex-replace-multiple-br-tags-with-one-br-tag
				array_push($ResultArray, "$fn#####$path#####$type\n");
	//			echo "$mode ---> $fn#####$path<br />\n";
			}; # while( $sql_results = mysql_fetch_array($results)) { }
		} else {
		
		//		echo "Konnte die MySQL-Abfrage nicht verarbeiten / Could execute mysql query <br />\n";
		//		echo "MySQL-Antwort: " . mysql_error($conn);
		//		die();
				// send email or sms to me
				
		};  # if ( $MySqlArray ) {

		return $ResultArray;

	} // function SphinxSearch( $SearchQuery, $catg ){

	function SphinxSongtextSearch( $SearchQuery, $maxResults ){

		if ( !preg_match('/([\w+]|[\d+]|[^A-Za-z0-9]|[a-z]|[0-9]{2,})/i', $SearchQuery) || strlen($SearchQuery) <= 1 ){
		//	echo "SphinxSearch searchquery is null: $SearchQuery<br />";
			$ResultArray	= array();
			return $ResultArray;
		};
	
		$config 	= new Config();
		$db 		= $config->sql_sphinx_dbname();
		$conn 		= new Connection( );
		$conn->db( $db );

		$tbl 		= $config->sql_sphinx_table();
		$index		= $config->sphinx_index();
		
		$cl 		= new SphinxClient();
		$q 			= $SearchQuery;
		$sql 		= "";
		$host 		= "127.0.0.1";
		$port 		= 9312;
		$groupby 	= "";
		$groupsort 	= "@group desc";
		$filter 	= "group_id";
		$filtervals = array();
		$distinct 	= "";
		$sortby 	= "";
		$limit 		= $maxResults;
		$ranker 	= 'SPH_RANK_PROXIMITY_BM25';
		$select 	= "";
		$docids 	= array();

		//$mode = "SPH_MATCH_FULLSCAN";
		//$mode = "SPH_MATCH_ANY";
		//$mode = "SPH_MATCH_BOOLEAN";
		//$mode = "SPH_MATCH_EXTENDED";
		$mode = 'SPH_MATCH_EXTENDED2';
		//$mode = "SPH_MATCH_PHRASE";

		//echo "good";
		////////////
		// do query
		////////////127.0.0.1:9312
		$cl->SetServer("/var/run/searchd.sock", 0);
		//$cl->SetServer ( $host, $port );
		$cl->SetConnectTimeout ( 3 );
		$cl->SetArrayResult ( true );
		$cl->SetMatchMode ( $mode );
		$cl->SetFieldWeights ( array ( "title"=>100,"artist"=>65,"songtext"=>15, ) );
		$cl->ResetFilters();
		//$cl->SetSortMode( SPH_SORT_RELEVANCE );
		$cl->SetSortMode('SPH_SORT_RELEVANCE', "title");
		$cl->SetRankingMode( $ranker );
		$cl->SetGroupDistinct ( $distinct );

		//if ( $sortby )				$cl->SetSortMode ( SPH_SORT_EXTENDED, $sortby );
		//if ( $sortexpr )			$cl->SetSortMode ( SPH_SORT_EXPR, $sortexpr );

		$cl->SetSelect ( $select );
		$cl->SetLimits ( 0, $maxResults );
		//echo "good 2";
		$res = $cl->Query ( $q, $index );
		//echo "good 3";

		if ( $res===false )
		{
		//	echo "Query failed: " . $cl->GetLastError() . ".\n";

		} else {
			if ( $cl->GetLastWarning() ) {
			//	echo "WARNING: " . $cl->GetLastWarning() . "\n\n";
			};
			//echo count($res["matches"]); 
			//echo "<br>";
			
			if ( is_array($res["matches"]) )
			{
				$n = 1;
				foreach ( $res["matches"] as $docinfo )
				{
				//	echo "$n. doc_id=$docinfo[id], weight=$docinfo[weight]<br />";
					array_push($docids, $docinfo["id"]);

				}
			}
		}
		
	//	print_r($docids);

		$ResultArray	= array();

		if (count($docids) <= 0 ){
			return $ResultArray; // gib leeres array zurück
		};

		$SqlQuery	= "SELECT * FROM $tbl WHERE";
			for ($i=0;$i<=count($docids) - 1;$i++){
				if ($i>0){
					$SqlQuery .= "\"$docids[$i]\",";
				} elseif($i<=0){
					$SqlQuery .= " `id` IN (\"$docids[$i]\",";
				};
			};
			$SqlQuery = substr($SqlQuery,0,(strlen($SqlQuery)-1));
			$SqlQuery .=");";
			
		$MySqlArray 	= $conn->doSQLQuery( $SqlQuery );

		if ( $MySqlArray ) {
			while( $sql_results = mysql_fetch_array($MySqlArray)) {
				$fn		= $sql_results['title'];
				$path 	= $sql_results['artist'];
				$type 	= $sql_results['songtext'];
				
				$type 	= preg_replace('#(<br\ ?\/?>)+#i', '<br />', $type); # 
				$type 	= preg_replace('#(<br */?>\s*)+#i', '<br />', $type); # http://stackoverflow.com/questions/7738439/how-to-regex-replace-multiple-br-tags-with-one-br-tag
				array_push($ResultArray, "$fn#####$path#####$type\n");
	//		echo "$mode ---> $fn#####$path<br />\n";
			}; # while( $sql_results = mysql_fetch_array($results)) { }
		} else {
		
		//		echo "Konnte die MySQL-Abfrage nicht verarbeiten / Could execute mysql query <br />\n";
		//		echo "MySQL-Antwort: " . mysql_error($conn);
		//		die();
				// send email or sms to me
				
		};  # if ( $MySqlArray ) {

		return $ResultArray;

	} // function SphinxSearch( $SearchQuery, $catg ){

/*
	function RandomResults( $catg, $result_count ){

		$config 	= new Config();
		$maxresults = $config->sql_max_results();
		$db 		= $config->sql_dbname();
		$conn 		= new Connection( );
		$conn->db( $db );

		if ( strcasecmp($catg,"movies")==0){
			$tbl 		= $config->sql_table_movies();
			$index		= "movies";
		} else if ( strcasecmp($catg,"porn")==0){
			$tbl 		= $config->sql_table_porn();
			$index		= "porn";
		} else if ( strcasecmp($catg,"series")==0){
			$tbl 		= $config->sql_table_series();
			$index		= "series";
		} else if ( strcasecmp($catg,"comics")==0){
			$tbl 		= $config->sql_table_comics();
			$index		= "comics";	
		} else if ( strcasecmp($catg,"kids")==0){
			$tbl 		= $config->sql_table_comics();
			$index		= "kids";
		} else if ( strcasecmp($catg,"anime")==0){
			$tbl 		= $config->sql_table_comics();
			$index		= "anime";		
		} else { // erotica
			$tbl 		= $config->sql_table_movies();
			$index		= "movies";
		}

		$ResultArray	= array();
		$SqlQuery 		= "SELECT * FROM $tbl ORDER BY RAND() LIMIT $result_count;" ;

		$MySqlArray 	= $conn->doSQLQuery( $SqlQuery );

		if ( $MySqlArray ) {
			while( $sql_results = mysql_fetch_array($MySqlArray)) {
				$fn 	= $sql_results['sname'];
				$fn_i	= $sql_results['imdb_title'];
				$path 	= $sql_results['path'];
				$type 	= $sql_results['type'];
				$size 	= $sql_results['size'];
			//	if ( strcasecmp($catg,"movies")==0){
			//		$fn = $fn_i . " - " . $fn;
			//	}
				array_push($ResultArray, "$fn#####$size#####$path#####$type\n");
			}; # while( $sql_results = mysql_fetch_array($results)) { }
		} else {
			
	//		echo "Konnte die MySQL-Abfrage nicht verarbeiten / Could execute mysql query <br />\n";
	//		echo "MySQL-Antwort: " . mysql_error($conn);
	//		die();
			// send email or sms to me
			
		};  # if ( $MySqlArray ) {

		return $ResultArray;
		
	} // function RandomResults( $catg, $result_count ){
*/
}
?>