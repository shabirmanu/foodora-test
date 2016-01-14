<?php 

namespace DBM;
use \PDO;
require_once('config.php');
/**
* This class provide interface for interacting with database and the data inside database
* @author Shabir A. Allshore Virtual Staffing
* 
*/

class DatabaseManager
{
  
  	private $conn;
	private $host;
	private $dsn;
	private $user;
	private $password;
	private $baseName;
	private $port;
	private $Debug; 
	private $query;
	private $dbh;
    private $error;

	/**
	* Constructor function to initialize values.
	*
	* @param array of credentials like database username, 
	* database name, password, host and port
	* @return void
	*/
	
	function __construct($params=array()) {
		$this->conn = false;
		$this->host = HOST; //hostname
		$this->user = USERNAME; //username
		$this->password = PASS; //password
		$this->dbname = DB_NAME; //name of your database
		$this->port = PORT;
		$this->debug = true;
		$this->dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
		$this->connect();
	}
    
    /**
	* private function to connect to database.
	*
	* @param array of credentials like database username, 
	* database name, password, host and port
	* @return database handler
	*/
 
	 private function connect()
	 {
		$options = array(
        PDO::ATTR_PERSISTENT    => true,
        PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
        // Create a new PDO instanace
        try{
            $this->dbh = new \PDO($this->dsn, $this->user, $this->password, $options);
        }
        // Catch any errors
        catch(PDOException $e){
            $this->error = $e->getMessage();
        }
	 }

	/**
	 * Updates regular days to special days.
	 *
	 */
	public function takeBackup() {
		$query = 'CREATE TABLE IF NOT EXISTS temp  AS (SELECT * FROM vendor_schedule)';
		$q = $this->dbh->prepare($query);
		$q->execute();
		$affected_rows = $q->rowCount();
		return $affected_rows;

	}
	/**
	 * Updates special days to regular days.
	 *
	 */
	public function runQuery($query) {
		$q = $this->dbh->prepare($query);
		$q->execute();
		$affected_rows = $q->rowCount();
		return $affected_rows;
	}

	public function dropTemp() {
		$query = 'DROP TABLE IF EXISTS temp';
		$q = $this->dbh->prepare($query);
		$q->execute();
		$affected_rows = $q->rowCount();
		return $affected_rows;
	}
}



