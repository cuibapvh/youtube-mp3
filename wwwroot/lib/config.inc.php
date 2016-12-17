<?php


 class Config {

	private $templatepath_std; 
	
	private $sql_hostname ;
	private $sql_dbname ;
	private $sql_tablename ;
	private $sql_username ;
	private $sql_password ;
	
	private $perl_exec;
	private $perl_script_path;
	
	private $mp3_store_path;
	private $mp3_download_uri;
	
	private $sql_sphinx_dbname;
	private $sphinx_index;
	private $sql_max_results;
	private $sql_sphinx_table;
	private $sql_max_sphinx_mp3_integration_results;
	
	private $yt_result_count;
	private $yt_result_count_max;
	private $yt_songtext_count;
	
	private $lyrics_dir_title_tag;
	private $lyrics_dir_database;
	private $lyrics_dir_database_songtextid;
	private $lyrics_dir_results_per_page;
	private $lyrics_dir_pagecount_per_page;
	private $lyrics_search_count;
	private $lyrics_startpage_direntry_count;
	
	private $ajax_state_temp_path;
	private $download_store_temp_path;
	
	private $fmpeg_bin;
	
	public function __construct() {
		
		$this->ffmpeg_bin 		= "/usr/local/bin/ffmpeg";
		$this->ajax_state_temp_path = "/home/ajax/temp";
		$this->download_store_temp_path = "/home/wwwyoutube/tmp";
		
		$this->lyrics_dir_title_tag = "Lyrics, Liedtext und Songtext Verzeichnis"; 
		$this->lyrics_dir_database = "lyricsverzeichnis";
		$this->lyrics_dir_results_per_page = 65;
		$this->lyrics_dir_database_songtextid = "songtexts";
		$this->lyrics_dir_pagecount_per_page = 7;
		$this->lyrics_search_count = 15;
		$this->lyrics_startpage_direntry_count = 10;
		
		$this->sql_hostname = "localhost"; 
		$this->sql_dbname = "youtube";
				
		$this->sql_tablename ="converter";
		$this->sql_username ="";
		$this->sql_password = "";
		$this->sql_sphinx_dbname = "songtexts";
		$this->sql_sphinx_table = "songtexts";
		
		$this->templatepath_std = "/home/wwwyoutube/tpl"; 
		
		$this->perl_exec = "/usr/bin/perl"; 
	#	VERSION 3 : $this->perl_script_path = "/home/wwwyoutube/exec/convertV3.pl"; 
		$this->perl_script_path = "/home/wwwyoutube/convert/convertV4.pl"; 
		
		$this->mp3_store_path = "/home/wwwyoutube/storeMP3save2014"; 
		$this->mp3_download_uri = "http://www.youtube-mp3.mobi/download.php?mp3="; 
		
		$this->sphinx_index = "songtext"; 
		$this->sql_max_sphinx_mp3_integration_results 	= 3;
		
		$this->yt_result_count = 15;
		$this->yt_result_count_max = 3;
		
		$this->yt_songtext_count = 2500; //2502045;
		
	} // public function __construct() {
	
	public function ffmpeg_bin(){
		return $this->ffmpeg_bin;
	}
	public function download_store_temp_path(){
		return $this->download_store_temp_path;
	}
	public function ajax_state_temp_path(){
		return $this->ajax_state_temp_path;
	}
	public function lyrics_startpage_direntry_count(){
		return $this->lyrics_startpage_direntry_count;
	}
	public function lyrics_search_count(){
		return $this->lyrics_search_count;
	}
	public function lyrics_dir_pagecount_per_page(){
		return $this->lyrics_dir_pagecount_per_page;
	}
	public function lyrics_dir_database_songtextid(){
		return $this->lyrics_dir_database_songtextid;
	}
	public function lyrics_dir_results_per_page(){
		return $this->lyrics_dir_results_per_page;
	}
	public function lyrics_dir_database(){
		return $this->lyrics_dir_database;
	}
	public function lyrics_dir_title_tag(){
		return $this->lyrics_dir_title_tag;
	}
	public function yt_songtext_count(){
		return $this->yt_songtext_count;
	}
	public function yt_result_count_max(){
		return $this->yt_result_count_max;
	}
	public function yt_result_count(){
		return $this->yt_result_count;
	}
	public function sql_max_sphinx_mp3_integration_results(){
		return $this->sql_max_sphinx_mp3_integration_results;
	}
	public function sql_sphinx_table(){
		return $this->sql_sphinx_table;
	}
	public function sql_max_results(){
		return $this->sql_max_results;
	}
	
	public function sphinx_index(){
		return $this->sphinx_index;
	}
	public function sql_sphinx_dbname(){
		return $this->sql_sphinx_dbname;
	}
	
	public function mp3_download_uri(){
		return $this->mp3_download_uri;
	}
	public function mp3_store_path(){
		return $this->mp3_store_path;
	}
	public function perl_exec(  ){
		return $this->perl_exec;
	}	
	public function perl_script_path(  ){
		return $this->perl_script_path;
	}	
	
	public function sql_hostname(  ){
		return $this->sql_hostname;
	}	
	public function sql_dbname(  ){
		return $this->sql_dbname;
	}	
	public function sql_tablename(  ){
			return $this->sql_tablename;
		}	
	public function sql_username(  ){
			return $this->sql_username;
		}	
	public function sql_password(  ){
			return $this->sql_password;
		}			
		
	public function getTemplatePath( $param ){
		if ( $param == 'index_page' ) {
			return $this->templatepath_std;
		}
		if ( $param == 'standard_page' ) {
			return $this->templatepath_std;
		}
	}

}

/*
public function (  ){
		return $this->
	}	
*/
?>
