<?php

namespace application\lib\Validator\Rules\Core;

use application\lib\Validator\Rules\AbstractRule;

class IsNull extends AbstractRule
{

    public function isValid($input = null)
    {
        return $input === null;
    }
}
