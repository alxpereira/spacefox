<?php
require_once __DIR__.'/../_lib/spyc.php';
/**
 * Spacefox -- Cool & Simple MVC PHP Framework
 * @version 0.0.2
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
class spacefox_install extends spacefox{
    private static $_sf_dbname = "spacefox_core";

	/** 
	 * Init Function
	 * 
	*/
	public static function init_install(){
        self::init(false);

        self::load_install_template("header_install");
        self::install_msg("Initializing Installation of spacefox...", null);

        self::make_dbs();

        self::make_models();

        self::install_msg("Install done bro' :)", null);
        self::load_install_template("footer_install");
	}

    /**
     * Databases Creation trigger
    */
    private static function make_dbs(){
        $config = self::$_config;
        if(!$config['db_enable']){
            self::front_log([
                "success" => 0,
                "log" => "Database isn't enabled in /_spacefox/config.yml, skipping this step",
            ]);
            return;
        }

        if(!spacefox_db::check_db("spacefox_core")){
            spacefox_install::make_sf_db();
        }else{
            self::install_msg("Database <strong>'spacefox_core'</strong> already exists skipping this step... ", "info");
        }

        if(!spacefox_db::check_db($config['db_name'])){
            spacefox_install::make_client_db();
        }else{
            self::install_msg("Database <strong>'".$config['db_name']."'</strong> already exists skipping this step... ", "info");
        }
    }

    /**
     * Models Creation trigger
    */
    private static function make_models(){
        $config = self::$_config;

        if(isset($config['models']) && spacefox_db::check_db($config['db_name'])){
            self::install_msg("Database <strong>'".$config['db_name']."'</strong> found and models detected in your config.yml installing...", "success");
            foreach($config['models'] as &$table){
                if(!spacefox_db::check_table($config['db_name'], $table)){
                    spacefox_db::_set_table($table, $table);
                    self::install_msg("Creating <strong>".$table."</strong> model in <strong>".$config['db_name']."</strong>", "success");
                }else{
                    self::install_msg("Model <strong>".$table."</strong> already exists skipping this step... ", "info");
                }
            }
        }else{
            return;
        }
    }

    /**
     * Core Database Install
     * @TODO: Push back "already install messages"
    */
    private static function make_sf_db(){
        $log = "";

        $sql = "CREATE DATABASE IF NOT EXISTS ".self::$_sf_dbname;
        if(spacefox_db::exec_query($sql, self::$_sf_dbname, false)){
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
            $log .= " Error creating database";
        }

        $response = [
            "success" => $success,
            "log" => $log,
        ];
        self::front_log($response);
    }

    /**
     * Core Tables Install
    */
    private static function make_sf_tables(){
        $sql="CREATE TABLE applications(root CHAR(255),site_domain CHAR(255),db_name CHAR(255), installer_ip CHAR(255))";

        if(spacefox_db::exec_query($sql, self::$_sf_dbname)){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Core DB value insert
    */
    private static function insert_sf_db(){
        $config = self::$_config;
        try{
            $sql = "INSERT INTO applications (root, site_domain, db_name, installer_ip) VALUES (:root,:site_domain,:db_name,:installer_ip)";
            $q = spacefox_db::_get_connect(self::$_sf_dbname)->prepare($sql);

            $q->execute(array(
                ':root'         => $config['root_folder'],
                ':site_domain'  => $config['domain'],
                ':db_name'      => $config['db_name'],
                ':installer_ip' => self::get_client_ip()
            ));
            return true;
        }catch (PDOException $e){
            return false;
        }
    }

    /**
	 * Client Database Install
     * @TODO: Push back "already install messages"
	*/
	private static function make_client_db(){
		$config = self::$_config;
		$log = "";

        if(spacefox_db::_set_db()){
            $success = 2;
            $log .= " Database '".$config['db_name']."' created successfully";
        }else{
            $success = 1;
            $log .= " Error creating database: ";
        };

		$response = [
			"success" => $success,
			"log" => $log,
		];
        self::front_log($response);
	}

    /**
     * Loading Installation template (html for header, footer etc...)
     * @param String $tpl_name - name of the html to load
    */
    private static function load_install_template($tpl_name){
        require __DIR__.'/../_install/_templates/'.$tpl_name.'.html';
    }

    /**
     * Retrieve client ip address
     * @return String $ip - ip address
    */
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