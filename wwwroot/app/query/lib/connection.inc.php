<?php
require_once ( "config.inc.php" );

class Connection {

	private $_host;
	private $_login;
	private $_password;
	private $_socket;
	private $results;
	
	private $MYSQL_PR_HOST;
	private $MYSQL_PR_USER;
	private $MYSQL_PR_PASS;
	private $MYSQL_PR_DATABASE;
	private $DBH;
	private $db;

	public function db($dba){
		$this->db = $dba;
	}
	
	public function doSQLQuery( $sql_query ){ 
			
		mysql_select_db($this->db, $this->_socket) OR die ("Error connecting to DB on $this->db, $this->_socket . Error Message: ".mysql_error($this->_socket));
		mysql_query('set names utf8', $this->_socket);
		$results	= mysql_query($sql_query , $this->_socket);
		mysql_close($this->_socket);
		return $results;

	}
		
	public function __construct() {

		$config = new Config();
		$MYSQL_PR_HOST = $config->sql_hostname();
		$MYSQL_PR_USER = $config->sql_username();
		$MYSQL_PR_PASS = $config->sql_password();
				
		$DBH = mysql_connect($MYSQL_PR_HOST, $MYSQL_PR_USER, $MYSQL_PR_PASS);
		$this->_socket = $DBH;
		
	/*
		$config = new Config();
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		if ($socket === false) {
			echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
			echo "Query Server down - inform thecerial@gmail.com ";
		} else {
			echo "OK.\n";
		}
		$result = socket_connect($socket, $config->getWorkingSlaveAdress(), $config->getWorkingSlavePort());
		if ($result === false) {
			echo "socket_connect() failed.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
			echo "Query Server down - inform thecerial"; // later: write mail:thecerial@gmail.com?subject=query down
		} else {
			echo "OK.\n";
			$this->_socket = $socket;
		}
	*/
	} // public function __construct() {

	public function sread(){
		$result = socket_read ($this->_socket, 1024*512, PHP_BINARY_READ);
		return $result;
	}
	
	public function swrite($data){
		echo "swrite(): '$data'\n";
		#$proto = "type=customer&###&$login&###&$subdomain&###&$pass&###&$server&###&$email";
		socket_write($this->_socket, $data."\r\n", strlen($data."\r\n"));
		return 1;
	}

}

?>