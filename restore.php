<?php
require 'DatabaseManager.php';

/**
* 
*/
class BackupRestorer
{
	private $script_date , $dbh;
	
	function __construct($cli_arguments, $db_handler) {
		
		$this->dbh = $db_handler;
		
		if(count($cli_arguments) > 1) {
			$date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])[\-\/.](0[1-9]|[12][0-9]|3[01])$/';

			if(isset($cli_arguments[1])) {
				if (!preg_match($date_regex, $cli_arguments[1])) {
				    file_put_contents('php://stderr', 'Your date entry does not match the YYYY-MM-DD required format.',FILE_APPEND);
				 	exit(0);
				}
			}
			$this->script_date = $cli_arguments[1];
		} else {
			file_put_contents('php://stderr', 'Please enter valid date to run the script.',FILE_APPEND);
		}

	}

	protected function _dropTempTable() {
		$this->dbh->dropTemp();
	}

	public function copyRegularDaysData() {
		// If backup successful
		$query = "TRUNCATE table vendor_schedule;
		insert into vendor_schedule select * from temp";
		
		$affected_rows = $this->dbh->runQuery($query);
		
		$this->_dropTempTable();
		
	}
}

#print_r($_SERVER);
/**
* Declaration of Class and run script
*/
$dbObj = new \DBM\DatabaseManager();
$sObj = new BackupRestorer($_SERVER['argv'], $dbObj);
$sObj->copyRegularDaysData();