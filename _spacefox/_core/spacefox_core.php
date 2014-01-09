<?php
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

		public static function sf_dump($message){
			var_dump($message);
		}

		public function route(){
			$devenv = false;
			$path = $_SERVER["REQUEST_URI"];

			$version = $path[0];
			$function = $path[1];
			$extraa = $path[2];

			echo $path;
		}
	}
?>