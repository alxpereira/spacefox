<?php
require_once './_lib/spyc.php';

/**
 * spacefox install class 
 * @package spacefox_install
 */
class spacefox_install{

	private static $_config;
	/** 
	 * Init Function
	 * 
	*/
	public function init(){
		$success = false;
		self::$_config = Spyc::YAMLLoad('config.yml');

		$dbinit = spacefox_install::makedb();
		switch ($dbinit['success']) {
			case 0:
				echo "nothing to do : ".$dbinit['log'];
				break;
			
			default:
				# code...
				break;
		}
	}

	/** 
	 * Config Object Get from config.yml file
	 * @return Array $config - parsed object
	*/
	public static function getconfig(){
		$config = $this->$_config;
		return $config;
	}

	/** 
	 * Database Install
	 * @return Array $response - response status and log
	*/
	private function makedb(){
		$config = self::$_config;
		$success = false;
		$log = "";

		if($config['db_enable'] == 'yes'){

			// DB enabled
			$con = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass']);
			// Check connection
			if (mysqli_connect_errno()){
		  		$log = 'Failed to connect to MySQL: ' . mysqli_connect_error();
		  	}

			// Create database
			$sql="CREATE DATABASE ".$config['db_name'];
			if (mysqli_query($con,$sql)){
				$success = true;
		 		$log .= " Database ".$config['db_name']." created successfully";
			}else{
		  		$log .= " Error creating database: " . mysqli_error($con);
			}

		}else{
			// DB not enabled
			$success = 0;
			$log = "Database not enabled in _spacefox/config.yml";
		}

		$response = [
			"success" => $success,
			"log" => $log,
		];
		return $response;
	}
}

?>