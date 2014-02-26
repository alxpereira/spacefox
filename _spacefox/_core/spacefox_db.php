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
     * Install table on the customer declared DB (inherit from _set_table)
     * @param String $table_name - name of the table to create
     * @param String $model_name - name of the model declared in /models/{model_name}.yml
     *
     * @return Boolean - true/false if the creation succeeded of failed.
    */
    private function create_table($table_name, $model_name){
        $config = self::$_config;

        $model = self::model($model_name);
        $model_request = "";

        $index = 1;
        foreach ($model as $key => $value) {
            $new_row = ($index != count($model)) ? $key." ".$value.", " : $key." ".$value;

            $model_request.=$new_row;
            $index++;
        }

        $dbhost = strlen($config['db_port']) > 0 ? $config['db_host'].":".$config['db_port'] : $config['db_host'];
        $con = mysqli_connect($dbhost, $config['db_user'], $config['db_pass'], $config['db_name']);

        $sql="CREATE TABLE ".$table_name."(".$model_request.")";
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
}