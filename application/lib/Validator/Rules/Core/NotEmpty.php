<?php

namespace application\lib\Validator\Rules\Core;

use application\lib\Validator\Rules\AbstractRule;

class NotEmpty extends AbstractRule{

    public function isValid($input = null){
        return $input !== null || $input!='';
    }
}