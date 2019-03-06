<?php

namespace application\core;

use application\core\View;

abstract class Controller{

    protected $route;
    public $view;

    public function __construct($route){
        $this->route=$route;
        $this->view = new View($route);
        $this->model=$this->loadModel($route['controller']);
    }

    public function loadModel($name){
        $path = 'application\models\\'.ucfirst($name);
        if(class_exists($path)){
            return new $path;
        }
    }
}