<?php
/**
 * spacefox -- Cool & Simple MVC PHP Framework
 * @version 0.0.2
 * @author Alexandre Pereira <alex.was.pereira@gmail.com>
 * @link https://github.com/alxpereira/spacefox
 * @copyright Copyright 2014 Alexandre Pereira
 * @license WTFPL 2004
 * @package spacefox
 */


/**
 * spacefox_db class for DB management
 * @package spacefox_forge
 */
class spacefox_db extends spacefox{
    /**
     * pdo connection (null by default)
     * @var null
    */
    public static $_pdo = null;

    /**
     * Get connection to the SQL entity
     * @param String $target_db - target db name
     * @param Boolean $ondb - should we target a db?
     *
     * @return pdo - connection created
    */
    public static function _get_connect($target_db = null, $ondb = true){
        $conn = self::connect_to_db($target_db, $ondb);
        return $conn;
    }

    /**
     * Database creation from config.yml
     * @return bool - true/false if the creation succeeded of failed.
    */
    public static function _set_db(){
        return self::create_db();
    }

    /**
     * Install table on the customer declared DB
     * @param String $table_name - name of the table to create
     * @param String $model_name - name of the model declared in /models/{model_name}.yml
     *
     * @return Boolean - true/false if the creation succeeded of failed.
    */
    public static function _set_table($table_name, $model_name){
        return self::create_table($table_name, $model_name);
    }

    /**
     * Return parameters of a model
     * @param String $model_name - name of the model declared in /models/{model_name}.yml
     *
     * @return Array $model - model and details
    */
    public static function model($model_name){
        $model = Spyc::YAMLLoad(__DIR__.'/../models/'.$model_name.'.yml');
        return $model;
    }

    /**
     * Get connection to the SQL entity
     * @param String $target_db - target db name
     * @param Boolean $ondb - should we target a db?
     *
     * @return pdo - connection created
    */
    private function connect_to_db($target_db, $ondb){
        try {
            $config = self::$_config;
            $dbhost = strlen($config['db_port']) > 0 ? $config['db_host'].":".$config['db_port'] : $config['db_host'];

            // if no nature in the config.yml file -> mysql
            $nature = strlen($config['db_nature']) > 0 ? $config['db_nature'] : "mysql";

            $db_name = $target_db != null ? $target_db : $config['db_name'];

            $target_dbreq = $ondb ? ";dbname=".$db_name : "";

            $pdo = new PDO("$nature:host=$dbhost".$target_dbreq, $config['db_user'], $config['db_pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            self::$_pdo = $pdo;

            return $pdo;
        }
        catch(PDOException $e)
        {
            $_msg = $e->getMessage();
            self::logger('error', 'Spacefox DB : Connection error : '.$_msg);
        }
    }

    /**
     * Database creation from config.yml
     * @return bool - true/false if the creation succeeded of failed.
    */
    private function create_db(){
        $db_name = self::$_config["db_name"];

        $sql = "CREATE DATABASE IF NOT EXISTS $db_name";
        return self::exec_query($sql, $db_name, false);
    }

    /**
     * Install table on the customer declared DB (inherit from _set_table)
     * @param String $table_name - name of the table to create
     * @param String $model_name - name of the model declared in /models/{model_name}.yml
     *
     * @return Boolean - true/false if the creation succeeded of failed.
    */
    private function create_table($table_name, $model_name){
        $model = self::model($model_name);
        $model_request = "";

        $index = 1;
        foreach ($model as $key => $value) {
            $new_row = ($index != count($model)) ? $key." ".$value.", " : $key." ".$value;

            $model_request.=$new_row;
            $index++;
        }

        $sql="CREATE TABLE ".$table_name."(".$model_request.")";

        return self::exec_query($sql);
    }

    /**
     * Execute SQL query
     * @param String $q - SQL query
     * @param String $target_db - target db name
     * @param Boolean $ondb - should we target a db?
     *
     * @return Boolean - true/false if the query succeeded of failed.
    */
    public function exec_query($q, $target_db = null, $ondb = true){
        $pdo = self::_get_connect($target_db, $ondb);
        try {
            $pdo->exec($q);
            return true;
        } catch (PDOException $e) {
            $_msg = $e->getMessage();
            self::logger('error', 'Spacefox DB : error : '.$_msg);
            return false;
        }
    }
}