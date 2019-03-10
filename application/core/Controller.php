<?php

namespace application\core;

use application\core\View;

abstract class Controller{

    protected $model;
    protected $view;

    public function __construct(Model $model, View $view){
       $this->model=$model;
       $this->view=$view;
    }

    public function loadModel($name){
        $path = 'application\models\\'.ucfirst($name);
        if(class_exists($path)){
            return new $path;
        }
    }

    public function redirect($url){
        header('location: '.$url);
        exit;
    }
}