spacefox
========

SpaceFox is a new MVC PHP Framework, simple and fast for easy website production. 

Version
----
0.0.1

Install
-------------

##### Configure _spacefox/config.yml file with the following mandatory values

* root_folder
* domain

Display my first page ?
-------------

##### You want to display a simple page with no templating issue ? easy...

* edit you ```route_views``` node in **_spacefox/config.yml** to define which view will be displayed depending on the url called. That means that the url on the left will call the view on the right. Easy ! example :

```sh
route_views :
   "/"     : "home"
   "/test" : "test"
```

* then just create a PHP file in the **_spacefox/views/** folder with the same name and write you html/php code inside. Here for example they will be :  
 
```sh
/views/
    home.php
    test.php
```

API controls
-------------

##### You want a simple api to trigger a function when calling a specific url ?

* edit you ```route_api``` node in **_spacefox/config.yml** to set the url that will trigger a function in a class. example here: 

```sh
route_api :
  "/api/hello" : "test_api => dosomething"
  
# the url "/api/hello" will run the function dosomething in the "class test_api"
```

* then just create a PHP file in the **_spacefox/controls/api/** folder names as the class name. Here for example they will be :  

```sh
/controls/
    /api/
        test_api.php
```

* and write your class/method in the file just created 

```sh
<?php
    class test_api{
        public function dosomething(){
            echo "hello world";
        }
    }
?>
```

#### Result
The url http://yourwebsite.com/api/hello will display "hello world" :)

Coming soon...
-------------
* Databases support (mySQL) and spacefox vendor DB utilities
* Templating options

License
----

WTFPL (Do What the Fuck You Want to Public License) 2014

**(Copyleft) Alexandre Pereira**

    