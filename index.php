<?php

use application\core\App;

error_reporting(E_ALL);
ini_set("display_errors", 1);

function deb($var){
    var_dump($var);
}

include_once __DIR__.'/autoload.php';

$config = include __DIR__.'/application/config/config.php';

(new App($config))->run();


