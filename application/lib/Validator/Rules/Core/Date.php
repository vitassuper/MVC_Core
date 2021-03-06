<?php

namespace application\lib\Validator\Rules\Core;

use application\lib\Validator\Rules\AbstractRule;

class Date extends AbstractRule
{
    const PATTERN = '/^(\d{4})-(\d{2})-(\d{2})$/';

    public function isValid($input = null)
    {
        if ($input === null || $input === '') {
            return false;
        }

        $input = (string) $input;

        if (!preg_match(static::PATTERN, $input, $matches)) {
            $this->violations[] = 'format';

            return false;
        }

        if (!checkdate($matches[2], $matches[3], $matches[1])) {
            $this->violations[] = 'date';

            return false;
        }

        return true;
    }
}
