<?php
	
require_once ( "config.inc.php" );

class Database {
	
	public $db;
	
	function __construct(){
        $config = new Config();
		$this->db = new SQLiteDatabase( $config->getPornSearchDB(), SQLITE3_OPEN_READWRITE); 
    }
	function doQuery( $sql ) {
		if ( strlen(sql)>10 ) {
			return $this->query($sql);
		}
	}

}

/*
// iterate through the retrieved rows

while ($result->valid()) {

    // fetch current row

    $row = $result->current();     

    print_r($row);

// proceed to next row

    $result->next();

}
*/
?>



