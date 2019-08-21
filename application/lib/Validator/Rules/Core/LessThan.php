<?php

namespace application\lib\Validator\Rules\Core;

use application\lib\Validator\Rules\AbstractComparisonRule;

class LessThan extends AbstractComparisonRule
{

    public function isValid($input = null)
    {
        return $input < $this->value;
    }
}
