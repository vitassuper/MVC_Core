<?php

namespace application\core;

use application\core\View;

abstract class Controller{

    protected $model;
    protected $view;
    protected $request;

    public function __construct(Model $model, View $view, ServerRequest $request){
       $this->model=$model;
       $this->view=$view;
       $this->request=$request;
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