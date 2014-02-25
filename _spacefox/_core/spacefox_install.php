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
    private static $_sf_dbname = "spacefox_core";

	/** 
	 * Init Function
	 * 
	*/
	public static function init(){
        self::load_install_template("header_install");

        self::install_msg("Initializing Installation of spacefox...", null);
		self::$_config = Spyc::YAMLLoad(__DIR__.'/../config.yml');

        self::make_dbs();

        self::install_msg("Install done bro' :)", null);
        self::load_install_template("footer_install");
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
    private static function install_log($log){
        switch ($log['success']) {
            case 0:
                $msg =  "Nothing to do : ".$log['log'];
                $msg_style = "info";
                break;

            case 1:
                $msg = "Error : ".$log['log'];
                $msg_style = "error";
                break;

            case 2:
                $msg = "Success : ".$log['log'];
                $msg_style = "success";
                break;

            default:
                $msg = "";
                $msg_style = null;
                break;
        }

        self::install_msg($msg, $msg_style);
    }

    /**
     * Databases Creation trigger
    */
    private static function make_dbs(){
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
    private static function make_sf_db(){
        $config = self::$_config;
        $log = "";

        $dbhost = strlen($config['db_port']) > 0 ? $config['db_host'].":".$config['db_port'] : $config['db_host'];
        $con = mysqli_connect($dbhost, $config['db_user'], $config['db_pass']);

        if(!mysqli_select_db($con, self::$_sf_dbname))
        {
            // Create database
            $sql="CREATE DATABASE ".self::$_sf_dbname;
            if (mysqli_query($con,$sql)){
                $success = 2;
                $log .= " Database 'spacefox_core' created successfully";

                if(!self::make_sf_tables()){
                    self::install_msg("Error Creating the spacefox core tables... embarrassing", "error");
                }
                if(!self::insert_sf_db()){
                    self::install_msg("Error Creating the spacefox core values... embarrassing", "error");
                }
            }else{
                $success = 1;
                $log .= " Error creating database: " . mysqli_error($con);
            }
        }
        else
        {
            $success = 2;
            $log .= "spacefox db and tables already exist. skipping this step...";
        }
        mysqli_close($con);

        $response = [
            "success" => $success,
            "log" => $log,
        ];
        spacefox_install::install_log($response);
    }

    /**
     * Core Tables Install
    */
    private static function make_sf_tables(){
        $config = self::$_config;

        $dbhost = strlen($config['db_port']) > 0 ? $config['db_host'].":".$config['db_port'] : $config['db_host'];
        $con = mysqli_connect($dbhost, $config['db_user'], $config['db_pass'], self::$_sf_dbname);

        $sql="CREATE TABLE applications(root CHAR(255),site_domain CHAR(255),db_name CHAR(255), installer_ip CHAR(255))";
        if (mysqli_query($con,$sql)){
            mysqli_close($con);
            return true;
        }
        else
        {
            mysqli_close($con);
            return false;
        }
    }

    /**
     * Core DB value insert
    */
    private static function insert_sf_db(){
        $config = self::$_config;

        $dbhost = strlen($config['db_port']) > 0 ? $config['db_host'].":".$config['db_port'] : $config['db_host'];
        $con = mysqli_connect($dbhost, $config['db_user'], $config['db_pass'], self::$_sf_dbname);

        $sql = "INSERT INTO applications (root, site_domain, db_name, installer_ip) VALUES ('".$config['root_folder']."', '".$config['domain']."', '".$config['db_name']."', '".self::get_client_ip()."')";

        if (mysqli_query($con,$sql)){
            mysqli_close($con);
            return true;
        }
        else
        {
            mysqli_close($con);
            return false;
        }
    }

    /**
	 * Client Database Install
	*/
	private static function make_client_db(){
		$config = self::$_config;
		$success = false;
		$log = "";

        $dbhost = strlen($config['db_port']) > 0 ? $config['db_host'].":".$config['db_port'] : $config['db_host'];
        $con = mysqli_connect($dbhost, $config['db_user'], $config['db_pass']);
        // Check connection to mysql
        if (mysqli_connect_errno()){
            $log = 'Failed to connect to MySQL: ' . mysqli_connect_error();
        }

        if(!mysqli_select_db($con, $config['db_name']))
        {
            // Create database
            $sql="CREATE DATABASE ".$config['db_name'];
            if (mysqli_query($con,$sql)){
                $success = 2;
                $log .= " Database '".$config['db_name']."' created successfully";
            }else{
                $success = 1;
                $log .= " Error creating database: " . mysqli_error($con);
            }
        }
        else
        {
            $success = 2;
            $log .= $config['db_name']." db already exists. skipping this step...";
        }
        mysqli_close($con);

		$response = [
			"success" => $success,
			"log" => $log,
		];
        spacefox_install::install_log($response);
	}

    /**
     * Loading Installation template (html for header, footer etc...)
     * @param String $tpl_name - name of the html to load
    */
    private static function load_install_template($tpl_name){
        require __DIR__.'/../_core/_templates/'.$tpl_name.'.html';
    }

    /**
     * Install Msg Logs Generator
     * @param String $msg - message to send
     * @param String $template - range of the message to show (warn, info, etc...)
    */
    private static function install_msg($msg, $template){
        switch($template){
            case "success":
                $msg = "<div class=\"alert alert-success\">".$msg."</div>";
                break;
            case "info":
                $msg = "<div class=\"alert alert-info\">".$msg."</div>";
                break;
            case "warn":
                $msg = "<div class=\"alert alert-warning\">".$msg."</div>";
                break;
            case "error":
                $msg = "<div class=\"alert alert-danger\">".$msg."</div>";
                break;
            default:
                $msg = "<p>".$msg."</p>";
                break;
        }

        echo $msg;
    }

    private static function get_client_ip(){
        if (!empty($_SERVER['HTTP_CLIENT_IP'])){
            $ip=$_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else{
            $ip=$_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}