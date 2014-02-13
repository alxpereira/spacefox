<?php
/**
 * spacefox -- Cool & Simple MVC PHP Framework
 * @version 0.0.1
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
        public static function tpl_gen($template, $data){
            $url = __DIR__.'/../views/templates/'.$template.'.html';
            //include_once(__DIR__.'/../views/templates/'.$template.'.php');
            $html = htmlentities(file_get_contents($url));

            if(count($data) > 0){
                $output = preg_replace("/{{(.*?)}}/ime", "\$data['$1']", $html);
            }else{
                $output = $html;
            }

            echo html_entity_decode($output);
        }
    }