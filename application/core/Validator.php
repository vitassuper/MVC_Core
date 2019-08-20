<?php

namespace application\core;

class Validator extends Rules{

    protected $data;

    public function __construct($data){
        $this->data=$data;
    }

    public function validate(array $params){
        $errors=[];
        foreach(array_keys($params) as $key){
                $errors[] = $this->parse_params($this->data[$key], $params[$key]);
            }
        return $errors;
    }

    public static function make($data, array $params){

    }

    public function parse_params($data, $params){
        $errors=[];
        $rules = explode("|", $params);
        foreach($rules as $key){
            if(strstr($key, "required")){
                $errors[] = $this->required_rule($data);
            }
            if(strstr($key, "min")){
                $errors[] = $this->min_rule($data, 10);
            }
            if(strstr($key, "max")){
                $errors[] = $this->max_rule($data, 20);
            }
        }
        return $errors;
    }
}