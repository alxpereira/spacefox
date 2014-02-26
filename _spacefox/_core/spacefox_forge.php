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
     * spacefox_forge class for templating
     * @package spacefox_forge
     */

    class spacefox_forge{
        /**
         * Parse the template string to put values inside
         * @param String $replacements - object containing the data.
         * @param String $template - string containing the template where we want to inject the data.
         *
         * @return String - templated string.
        */
        private static function bind_to_template($replacements, $template)
        {
            return preg_replace_callback('/{{(.+?)}}/', function($matches) use ($replacements)
            {
                return $replacements[$matches[1]];
            }, $template);
        }

        /**
         * Parse the template string to put values inside
         * @param String $template - string containing the template where we want to inject the data.
         * @param String $data - object containing the data.
         *
        */
        public static function tpl_gen($template, $data){
            $url = __DIR__.'/../views/templates/'.$template.'.html';
            //include_once(__DIR__.'/../views/templates/'.$template.'.php');
            $html = htmlentities(file_get_contents($url));

            if(count($data) > 0){
                $output = self::bind_to_template($data, $html);
            }else{
                $output = $html;
            }
            echo html_entity_decode($output);
        }
    }