<?php

namespace application\core;

class Route{
    private $controller;
    private $action;
    private $model;
    private $params=[];

    public function __construct($controller, $action, $params=[]){
        $this->controller=$controller;
        $this->action=$action;
        $this->params=$params;
        $this->createPathModel();
        $this->createPathController();
        $this->createPathView($controller);
        $this->createAction();
    }

    public function createPathController(){
        $this->controller='application\controllers\\'.ucfirst($this->controller).'Controller';
    }

    public function createPathView($controller){
        $this->view='application\views\\'.$controller.'\\'.$this->action;
    }

    public function createPathModel(){
        $this->model='application\models\\'.ucfirst($this->controller);
    }

    public function createAction(){
        $this->action=$this->action.'Action';
    }

    public function __get($property){
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}