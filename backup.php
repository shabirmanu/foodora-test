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
		$query = "
				UPDATE vendor_schedule vs INNER JOIN vendor_special_day ovsd on vs.weekday = (WEEKDAY(ovsd.special_date) + 1) 
				SET vs.weekday = WEEKDAY(ovsd.special_date) + 1, 
				vs.all_day = ovsd.all_day, vs.start_hour = ovsd.start_hour, 
				vs.stop_hour = ovsd.stop_hour";
			$special_days = $this->dbh->runQuery($query);
			$query = "Delete s1 from vendor_schedule s1, vendor_schedule s2 where s1.id > s2.id and s1.vendor_id = s2.vendor_id 
			and s1.weekday = s2.weekday and s1.all_day = s2.all_day and s1.start_hour = s2.start_hour and s1.stop_hour = s2.stop_hour";
			$dup_days = $this->dbh->runQuery($query);
		
	}
}

#print_r($_SERVER);
/**
* Declaration of Class and run script
*/
$dbObj = new \DBM\DatabaseManager();
$sObj = new FaultRestorer($_SERVER['argv'], $dbObj);
$sObj->updateSpecialDays();