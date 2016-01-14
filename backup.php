<?php
require 'DatabaseManager.php';

/**
* 
*/
class FaultRestorer
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

	protected function _takeBackup() {
		$this->dbh->takeBackup();
	}

	public function updateSpecialDays() {
		// If backup successful
		$this->_takeBackup();
			$query = "UPDATE vendor_schedule vs set vs.weekday = (select DAYOFWEEK(sd.special_date) from vendor_special_day sd 
				where sd.id = vs.id) , vs.all_day = (select sd.all_day from vendor_special_day sd where sd.id = vs.id) ,
	            vs.start_hour = (select sd.start_hour from vendor_special_day sd where sd.id = vs.id) ,
	            vs.stop_hour = (select sd.stop_hour from vendor_special_day sd where sd.id = vs.id)";
			
			$special_days = $this->dbh->runQuery($query);
		
	}
}

#print_r($_SERVER);
/**
* Declaration of Class and run script
*/
$dbObj = new \DBM\DatabaseManager();
$sObj = new FaultRestorer($_SERVER['argv'], $dbObj);
$sObj->updateSpecialDays();