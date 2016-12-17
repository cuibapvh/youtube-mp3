<?php
require_once ( "config.inc.php" );
require_once ( "connection.inc.php" );
require_once ( "sphinxapi.inc.php" );

class Search {

	function SphinxSearch( $SearchQuery, $catg ){

		if ( !preg_match('/([\w+]|[\d+]|[^A-Za-z0-9]|[a-z]|[0-9]{2,})/i', $SearchQuery) || strlen($SearchQuery) <= 1 ){
		//	echo "SphinxSearch searchquery is null: $SearchQuery<br />";
			$ResultArray	= array();
			return $ResultArray;
		};
	
		$config 	= new Config();
		$maxresults = $config->sql_max_results();
		$db 		= $config->sql_dbname();
		$conn 		= new Connection( );
		$conn->db( $db );

		if ( strcasecmp($catg,"music")==0){
			$tbl 		= $config->sql_table_music();
			$index		= "music";
		} else if ( strcasecmp($catg,"movies")==0){
			$tbl 		= $config->sql_table_movies();
			$index		= "movies";
		} else if ( strcasecmp($catg,"porn")==0){
			$tbl 		= $config->sql_table_porn();
			$index		= "movies";
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
		} else { // movies
			$tbl 		= $config->sql_table_movies();
			$index		= "movies";
		}

		//echo "DEBUG: SEarching for $SearchQuery in $catg (index: $index)<br>";

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
		$cl->SetFieldWeights ( array ( "imdb_title"=>70,"sname"=>99,"name"=>85,"plot"=>75,"genres"=>55, "fuzzy_title"=>10 ) );
		$cl->ResetFilters();
		//$cl->SetSortMode( SPH_SORT_RELEVANCE );
		$cl->SetSortMode('SPH_SORT_RELEVANCE', "@weight DESC");
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
				//	echo "$n. doc_id=$docinfo[id], weight=$docinfo[weight]";
					array_push($docids, $docinfo[id]);

				}
			}
		}

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
					$fn 	= $sql_results['sname'];
					//$path 	= $config->wwwroot().$sql_results['path'];
					$path 	= $config->wwwroot()."/".$sql_results['path'];
					$type 	= $sql_results['type'];
					$size 	= $sql_results['size'];
					array_push($ResultArray, "$fn#####$size#####$path#####$type\n");
					//echo "$mode ---> $fn#####$sfn#####$size#####$path#####$type\n";
				}; # while( $sql_results = mysql_fetch_array($results)) { }
			} else {
			
			//		echo "Konnte die MySQL-Abfrage nicht verarbeiten / Could execute mysql query <br />\n";
			//		echo "MySQL-Antwort: " . mysql_error($conn);
			//		die();
					// send email or sms to me
					
			};  # if ( $MySqlArray ) {

		return $ResultArray;

	} // function SphinxSearch( $SearchQuery, $catg ){


	function RandomResults( $catg, $result_count ){

		$config 	= new Config();
		$maxresults = $config->sql_max_results();
		$db 		= $config->sql_dbname();
		$conn 		= new Connection( );
		$conn->db( $db );

		if ( strcasecmp($catg,"music")==0){
			$tbl 		= $config->sql_table_music();
			$index		= "music";
		} else if ( strcasecmp($catg,"movies")==0){
			$tbl 		= $config->sql_table_movies();
			$index		= "movies";
		} else if ( strcasecmp($catg,"porn")==0){
			$tbl 		= $config->sql_table_porn();
			$index		= "movies";
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
				$path 	= $config->wwwroot()."/".$sql_results['path'];
				//$path 	= $config->wwwroot().$sql_results['path'];
				$type 	= $sql_results['type'];
				$size 	= $sql_results['size'];
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

}
?>