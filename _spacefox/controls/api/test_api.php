<?php
    /*
     * TEST API
    */
    class test_api{
        public static function dosomething(){
            spacefox::sf_dump('heellloo andrew');
        }

        public static function retrievedb(){
            if(spacefox_db::_set_table('test_bis', 'test_model')){
                echo "table created !!!";
            }else{
                echo "error creating table";
            }
        }
    }
