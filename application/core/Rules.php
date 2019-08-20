<?php

namespace application\core;

class Rules{
    
    public function required_rule($data){
        if(strlen($data)==NULL){
          return "The field must not be empty";
        }
    }

    public function min_rule($data, $min){
        if(strlen($data)<=$min){
            strlen($data);
            return "The fild must be more then ${min} symbols";
        }
    }
    
    public function max_rule($data, $max){
        if(strlen($data)>=$max){
            return "The fild must be less then ${max} symbols";
        }
    }

}