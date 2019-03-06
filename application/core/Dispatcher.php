<?php

namespace application\core;

use application\core\View;


class Dispatcher{

    private $router;

    public function __construct($router){
        $this->router=$router;
    }

    public static function ErrorCode(){
        View::errorCode(404);
    }

    public function dispatch($request){
        $this->router->findRoute($request);
    }
}