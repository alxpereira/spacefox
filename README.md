spacefox
========
**spacefox** is a new MVC PHP Framework, simple and fast for easy website production. 

Version
----
0.0.2

Install
-------------

#### Make a copy of your _spacefox/config.sample.yml and name it config.yml. Then complete this file with the following mandatory values :

* root_folder
* domain

#### !! Caution !!
You should have installed on your server apache 2.* and PHP 5.4 minimum to use spacefox.

Display my first page ?
-------------

#### You want to display a simple page with no templating issue ? easy...

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


Templating : spacefox::forge
-------------
#### Need to include some templates ? Yes you can as said the white house guy.

spacefox (forge) template system is really simple. You can include templates in a view in a second with our without data parsing.

Example :

My view is in /views/home.php and is routed on the url http://yourwebsite.com/ root. As seen in the first tutorial above. Now I want to display a generic header and footer in my view. Let's do a template !
* Create a new template in **/views/templates/** for example **header.html** and **footer.html**
```sh
/views/
    /templates/
        header.html
        footer.html
```
* This **header.html** file will contain a dynamic title that could change regarding the view or a related method.
```sh
<!DOCTYPE html>
<html>
<head>
    <title>{{title}}</title>
</head>
```
* And we'll finally call this template pushing the data in the related view regarding this logic : ```spacefox::forge(stringTemplate, arrayData);```, the second parameter could be also ```NULL``` if you don't need to parse any data.
```sh
<?php
    // example calling a template and sending data into
    spacefox::forge('header', array(
        "title" => "Test Title"
    ));

    // if no data to send, just calling a template :)
    spacefox::forge('footer',NULL);
```


API controls
-------------

#### You want a simple api to trigger a function when calling a specific url ?

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
```

#### Result
The url http://yourwebsite.com/api/hello will display "hello world" :)



Logs ? spacefox::logger :-)
-------------
#### Managing errors is also easy
All critical errors in the framework are sent to ***_spacefox/error.log***. 

You can also use this method to generate errors and new files simply using the method ```spacefox::logger(stringFilename, stringMessage)```

For example this method will write "aie aie aie" in the file _spacefox/myerrors.log
```sh
<?php
    // for example a custom class, it should inherit of spacefox main class to use the method
    class test extends spacefox{
        public function outch(){
            spacefox::logger('myerrors', 'aie aie aie');
        }
    }
```

Coming soon...
-------------
* Databases support (mySQL) and spacefox vendor DB utilities

License
----

WTFPL (Do What the Fuck You Want to Public License) 2004

**(Copyleft) 2014 - Alexandre Pereira**