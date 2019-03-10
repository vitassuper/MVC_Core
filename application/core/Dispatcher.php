<?php

namespace application\core;

use application\core\View;

use applicaton\core\Route;



class Dispatcher{

    private $router;

    private $route;

    public function __construct($router){
        $this->router=$router;
    }

    public static function ErrorCode(){
        View::errorCode(404);
    }

    public function dispatch($request){
        try{
           $this->route=$this->router->findRoute($request);
           $controller=$this->route->controller;
           $model=$this->route->model;
           $view=$this->route->view;
           new $controller(new $model(), new View($view));
          
        }catch(NotFoundException $exception){
            View::error();
        }
    }
}