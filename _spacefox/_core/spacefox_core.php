<?php
require_once __DIR__.'/../_lib/spyc.php';
require_once __DIR__.'/../_core/spacefox_forge.php';
require_once __DIR__.'/../_core/spacefox_db.php';

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
   	 * Main spacefox class.  
     * @package spacefox
     */
	class spacefox {
        protected static $_config;

        /**
         * Init Function
         *
        */
        public static function init(){
            self::$_config = Spyc::YAMLLoad(__DIR__.'/../config.yml');
            self::route();
        }

        /**
         * Spacefox Dump
         * @param String $message - message to dump
        */
		public static function sf_dump($message){
			var_dump($message);
		}

        /**
         * Routing Function for view and api
         *
        */
		private static function route(){
			$path = $_SERVER["REQUEST_URI"];
            $path = explode("?",$path)[0];
            $views_route = self::$_config['route_views'];
            $api_route = self::$_config['route_api'];
            $tester = false;

            /**
             * Check first if any url match the current path in the route_api array in config.yml
            */
            if(count($api_route) > 0){
                while (list($route_url, $val) = each($api_route)){
                    if(explode(self::$_config['root_folder'], $path)[1] == $route_url){
                        try{
                            $api_tmp = explode(" => ", $val);
                            $file = $api_tmp[0];
                            $method = $api_tmp[1];
                            $file_path = __DIR__.'/../controls/api/'.$file.'.php';
                            if(file_exists($file_path)){
                                include_once($file_path);
                                call_user_func($file."::".$method);
                                $tester = true;
                            }else{
                                self::logger("error", "Error [500]: File '".$file_path."' for route '".explode(self::$_config['root_folder'], $path)[1]."' doesn't exists in spacefox");
                                self::fivehundred();
                            }
                        }catch(exception $e){
                            self::fivehundred();
                        }
                    }
                }
            }

            /**
             * Check then if any url match the current path in the route_views array in config.yml
            */
            if(!$tester && count($views_route) > 0){
                while (list($view_url, $val) = each($views_route))
                {
                    if(explode(self::$_config['root_folder'], $path)[1] == $view_url){
                        $file_path = __DIR__.'/../views/'.$val.'.php';
                        try{
                            if(file_exists($file_path)){
                                include_once($file_path);
                                $tester = true;
                            }else{
                                self::logger("error", "Error [500]: File '".$file_path."' for route '".explode(self::$_config['root_folder'], $path)[1]."' doesn't exists in spacefox");
                                self::fivehundred();
                            }
                        }catch(exception $e){
                            self::fivehundred();
                        }
                    }
                }
            }

            /**
             * If no reference is found (too baad...) -> 404 page
            */
            if(!$tester){
                self::fourofour();
            }
		}

        /**
         * 404 generation method
        */
        protected static function fourofour(){
            header('HTTP/1.0 404 Not Found');
            echo "404 error";
            exit();
        }

        /**
         * 500 generation method
        */
        protected static function fivehundred(){
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            echo "500 error";
            exit();
        }

        /**
         * Error Logger
         * @param String $status - name of the file to write, for example "error".log
         * @param String $msg - msg to write in the log file
        */
        public static function logger($status, $msg){
            $dir_path = dirname(__FILE__).'/../log';

            // @TODO : Change permissions on apache to allow php to create the dir/file
            /*if (!file_exists($dir_path)) {
                mkdir($dir_path, 0744);
            }*/

            $msg = $msg." ".self::get_srv_time();

            $file_handle = fopen($dir_path.'/'.$status.'.log', 'a');
            fwrite($file_handle, $msg."\n");
            fclose($file_handle);
        }

        /**
         * Templating generator
         */
        public static function forge($template, $data){
            spacefox_forge::tpl_gen($template, $data);
        }

        /**
         * Get Server time
         *
         * @return String $current_date - current date/time on the "YYYY-MM-DD HH:MM:SS DIFF" format
         */
        protected static function get_srv_time(){
            $tz = strlen(self::$_config['timezone']) > 0 ? self::$_config['timezone'] : 'Europe/Paris';

            $date = new DateTime(null, new DateTimeZone($tz));
            $current_date = $date->format('Y-m-d H:i:sP') . "\n";

            return $current_date;
        }
        
        /**
         * Get data from a URL using CURL
         * @param String $url - url to load.
         *
         * @return Object $data
         */
        protected function get_data($url) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);

            // Check if there's a proxy setting
            if(strlen(self::$_config['proxy']) > 0){
                $proxy_url = explode("@", self::$_config['proxy'])[1];
                $proxy_log = explode("@", self::$_config['proxy'])[0];

                curl_setopt($ch, CURLOPT_PROXY, $proxy_url);
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy_log);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $data = curl_exec($ch);
            echo curl_error($ch);
            curl_close($ch);
            return $data;
        }
	}