<?php
    /*
     * TEST API
    */
    class sample_api extends spacefox{
        public function test_db(){
            $db = spacefox_db::_set_db();
            echo $db;
        }

        public static function dosomething(){
            spacefox::sf_dump('heellloo');
        }

        public static function retrievedb(){
            if(spacefox_db::_set_table('guys', 'sample_model')){
                echo "table created !!!";
            }else{
                echo "error creating table";
            }
        }

        public function insertdb(){
            try{
                $sql = "INSERT INTO guys (first_name,last_name) VALUES (:first_name,:last_name)";
                $q = spacefox_db::_get_connect()->prepare($sql);

                $q->execute(array(
                    ':first_name'=> 'toto',
                    ':last_name'=> 'tata'
                ));
                echo "yeah";
            }catch (PDOException $e){
                echo $e->getMessage();
            }
        }
    }
