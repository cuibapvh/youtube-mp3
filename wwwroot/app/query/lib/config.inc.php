<?php


class Config {
	
	private $sql_hostname ;
	private $sql_dbname ;
	private $sql_username ;
	private $sql_password ;
	
	private $sql_table_movies;
	private $sql_table_series;
	private $sql_table_comics;
	private $sql_table_porn;
	private $sql_table_music;
	
	private $sql_max_results;
	private $host_ip;
	private $wwwroot;
	private $previewtime;
	
	private $different_servers_enabled;
	private $query_host;
	private $query_hosts_wwwoot;
	private $query_hosts_ip;
	private $max_random_results;
	
	public function __construct() {
		
		$this->sql_hostname = "localhost"; 
		$this->sql_dbname = "youtube";
		$this->sql_password = "rouTer99";
		$this->sql_username = "root";
		
		$this->sql_table_movies = "movies"; 
		$this->sql_table_series = "series";
		$this->sql_table_comics = "comics";
		$this->sql_table_porn = "movies";
		$this->sql_table_music = "music";
		
		$this->max_random_results = 10;
		$this->sql_max_results 	= 100;
		$this->host_ip 			= "176.9.19.144/app/c"; // ip of server giving out the mp4 downloads/streams
		$this->wwwroot 			= "/home/wwwyoutube/app/c";
		$this->previewtime		= 600; // 10 minutes
		
		$this->query_servers_enabled		= 0; //
		$this->query_hosts					= "https:///search.php";
		$this->query_hosts_wwwoot			= "/media/kubuntu/TOSHIBA EXT/BuzzerStar/";
		$this->query_hosts_ip				= "";
		
	} // public function __construct() {
	
	
	public function max_random_results(){
		return $this->max_random_results;
	}
	public function query_hosts(){
		return $this->query_hosts;
	}
	public function query_hosts_ip(){
		return $this->query_hosts_ip;
	}
	public function query_hosts_wwwoot(){
		return $this->query_hosts_wwwoot;
	}
	public function query_servers_enabled(){
		return $this->query_servers_enabled;
	}
	
	public function previewtime(){
		return $this->previewtime;
	}
	public function wwwroot(){
		return $this->wwwroot;
	}
	public function host_ip(){
		return $this->host_ip;
	}
	public function sql_max_results(){
		return $this->sql_max_results;
	}
	public function sql_hostname(){
		return $this->sql_hostname;
	}
	public function sql_dbname(){
		return $this->sql_dbname;
	}
	public function sql_password(){
		return $this->sql_password;
	}
	public function sql_username(){
		return $this->sql_username;
	}
		
	public function sql_table_movies(){
		return $this->sql_table_movies;
	}
	public function sql_table_music(){
		return $this->sql_table_music;
	}
	public function sql_table_series(){
		return $this->sql_table_series;
	}
	public function sql_table_comics(){
		return $this->sql_table_comics;
	}
	public function sql_table_porn(){
		return $this->sql_table_porn;
	}
	public function config_search( $noporn ){
		if ( $noporn == 1 ){
			$search = array("music"); //array("movies","series","comics");
		} else {
			$search = array("music");
		};
		return $search;
	}	
	public function search_modes(){
		return array("SPH_MATCH_PHRASE","SPH_MATCH_EXTENDED2","SPH_MATCH_FULLSCAN","SPH_MATCH_ALL","SPH_MATCH_ANY");
	}
}


?>
