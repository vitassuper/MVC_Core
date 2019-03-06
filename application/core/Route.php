<?php

namespace application\core;

class Route{
    public $controllers;
    public $action;
    public $params=[];

    public function __construct($controller, $action, $params){
        $this->controller=$controller;
        $this->action=$action;
        $this->params=$params;
    }
}