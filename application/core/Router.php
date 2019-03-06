<?php

namespace application\core;

use application\exceptions\NotFoundException;
use application\core\Route;

class Router {
    protected $routes = [];
    protected $params = [];
    public $route;

    public function __construct($config){
        foreach ($config as $key => $val) {
            $this->add($key , $val);
        }
    }

    public function add($route, $params){
        $route = '#^'.$route.'$#';
        $this->routes[$route]=$params;
    }

    public function isMatch($route, $request){
        $url = trim($request, '/');
            if(preg_match($route, $url)){
                return true;
            }
            return false;
        }
    

    public function createRoute(){
        $path = 'application\controllers\\'.ucfirst($this->params['controller']).'Controller';
         if(class_exists($path)){
            $action = $this->params['action'].'Action';
            if (method_exists($path, $action)){
                return new Route($path, $action, $this->params);
            }
        }
    }

    public function findRoute(ServerRequest $request){
        foreach ($this->routes as $route){
            if ($this->isMatch($route, $request)) {
                return $this->createRoute();
                }
            }
        }
        throw new NotFoundException();
    }
}