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
   	 * Main spacefox class.  
     * @package spacefox
     */
	class spacefox {
        private static $_config;

        /**
         * Init Function
         *
        */
        public function init(){
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
		public function route(){
			$path = $_SERVER["REQUEST_URI"];
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
                            $file = explode(" => ", $val)[0];
                            $method = explode(" => ", $val)[1];
                            include_once(__DIR__.'/../controls/api/'.$file.'.php');
                            call_user_func($file."::".$method);

                            $tester = true;
                        }catch(exception $e){
                            self::fivehundred();
                        }
                    }
                }
            }

            /**
             * Check then if any url match the current path in the route_views array in config.yml
            */
            if(count($views_route) > 0){
                while (list($view_url, $val) = each($views_route))
                {
                    if(explode(self::$_config['root_folder'], $path)[1] == $view_url){
                        try{
                            include_once(__DIR__.'/../views/'.$val.'.php');
                            $tester = true;
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
        private function fourofour(){
            header('HTTP/1.0 404 Not Found');
            echo "404 error";
            exit();
        }

        /**
         * 500 generation method
        */
        private function fivehundred(){
            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            echo "500 error";
            exit();
        }
	}
?>