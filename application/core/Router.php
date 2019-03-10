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
        $url = trim($request->requestUri(), '/');
            if(preg_match($route, $url)){
                return true;
            }
            return false;
        }
    
    public function createRoute(){
        $path = $this->params['controller'];
        $action = $this->params['action'];
        return new Route($path, $action, $this->params);
    }

    public function findRoute(ServerRequest $request){
        foreach ($this->routes as $route => $params){
            if ($this->isMatch($route, $request)) {
                $this->params=$params;
                return $this->createRoute();
                }
            }
            throw new NotFoundException();
        }
}