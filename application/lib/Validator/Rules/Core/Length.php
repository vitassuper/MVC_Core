<?php

namespace application\lib\Validator\Rules\Core;

use InvalidArgumentException;
use LogicException;
use application\lib\Validator\Rules\AbstractRule;

class Length extends AbstractRule
{
  
    private $min;

    private $max;

    private $charset;

    public function __construct(
        $min = null,
        $max = null,
        $charset = 'UTF-8'
    ) {
        if ($min === null && $max === null) {
            throw new InvalidArgumentException('Either option "min" or "max" must be given.');
        }

        if ($min !== null && $max !== null && $min > $max) {
            throw new LogicException('"Min" option cannot be greater that "max".');
        }

        if ($max !== null && $max < $min) {
            throw new LogicException('"Max" option cannot be less that "min".');
        }

        $this->min = $min;
        $this->max = $max;
        $this->charset = $charset;
    }

    public function isValid($input = null)
    {
        if ($input === null || $input === '') {
            return false;
        }

        $input = (string) $input;

        if (!$invalidCharset = !@mb_check_encoding($input, $this->charset)) {
            $length = mb_strlen($input, $this->charset);
        }

        if ($invalidCharset) {
            $this->violations[] = 'charset';

            return false;
        }

        if ($this->max !== null && $length > $this->max) {
            $this->violations[] = 'max';

            return false;
        }

        if ($this->min !== null && $length < $this->min) {
            $this->violations[] = 'min';

            return false;
        }

        return true;
    }
}
