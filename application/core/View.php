<?php
namespace application\core;

class View {
    public $path;
    public $layout = 'default';
    public $route;

    public function __construct($route){
       $this->route=$route;
    }

    public function render($title, $vars =[]){
        extract($vars);
        $path = $this->route.'.php';
        if (file_exists($path)){
            ob_start();
            require $path;
            $content = ob_get_clean();
            require 'application/views/layouts/'.$this->layout.'.php';
        }
    }

    public static function errorCode($code){
        http_response_code($code);
        $path = 'application/views/errors/'.$code.'.php';
        if(file_exists($path)){
        require $path;
        }
        exit;
    }

}