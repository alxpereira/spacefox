<?php
require_once __DIR__.'/../_lib/spyc.php';
/**
 * Spacefox -- Cool & Simple MVC PHP Framework
 * @version 0.0.1
 * @author Alexandre Pereira <alex.was.pereira@gmail.com>
 * @link https://github.com/alxpereira/spacefox
 * @copyright Copyright 2014 Alexandre Pereira
 * @license WTFPL 2004
 * @package Spacefox
 */


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
		self::$_config = Spyc::YAMLLoad(__DIR__.'/../config.yml');

        self::make_dbs();

        echo "Install done bro'";
	}

	/** 
	 * Config Object Get from config.yml file
	 * @return Array $config - parsed object
	*/
	public static function getconfig(){
		$config = spacefox_install::$_config;
		return $config;
	}

    /**
     * Install Log Generator
     * @param Array $log - containing log details to show
    */
    private function install_log($log){
        switch ($log['success']) {
            case 0:
                echo "Nothing to do : ".$log['log'];
                break;

            case 1:
                echo "Error : ".$log['log'];
                break;

            case 2:
                echo "Success : ".$log['log'];
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * Databases Creation trigger
    */
    private function make_dbs(){
        $config = spacefox_install::$_config;
        if($config['db_enable'] != true){
            spacefox_install::install_log([
                "success" => 0,
                "log" => "Database isn't enabled in /_spacefox/config.yml, skipping this step",
            ]);
            return;
        }

        spacefox_install::make_sf_db();
        spacefox_install::make_client_db();
    }

    /**
     * Core Database Install
    */
    private function make_sf_db(){
        $config = self::$_config;
        $success = false;
        $log = "";

        $dbhost = strlen($config['db_port']) > 0 ? $config['db_host'].":".$config['db_port'] : $config['db_host'];
        $con = mysqli_connect($dbhost, $config['db_user'], $config['db_pass']);

        // Create database
        $sql="CREATE DATABASE IF NOT EXISTS spacefox_core";
        if (mysqli_query($con,$sql)){
            $success = 2;
            $log .= " Database 'spacefox_core' created successfully";
        }else{
            $success = 1;
            $log .= " Error creating database: " . mysqli_error($con);
        }

        $response = [
            "success" => $success,
            "log" => $log,
        ];
        spacefox_install::install_log($response);
    }

	/** 
	 * Client Database Install
	*/
	private function make_client_db(){
		$config = self::$_config;
		$success = false;
		$log = "";

        $dbhost = strlen($config['db_port']) > 0 ? $config['db_host'].":".$config['db_port'] : $config['db_host'];

        $con = mysqli_connect($dbhost, $config['db_user'], $config['db_pass']);
        // Check connection to mysql
        if (mysqli_connect_errno()){
            $log = 'Failed to connect to MySQL: ' . mysqli_connect_error();
        }

        // Create database
        $sql="CREATE DATABASE ".$config['db_name'];
        if (mysqli_query($con,$sql)){
            $success = 2;
            $log .= " Database '".$config['db_name']."' created successfully";
        }else{
            $success = 1;
            $log .= " Error creating database: " . mysqli_error($con);
        }

		$response = [
			"success" => $success,
			"log" => $log,
		];
        spacefox_install::install_log($response);
	}
}

?>