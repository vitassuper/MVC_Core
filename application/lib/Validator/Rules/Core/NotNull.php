<?php

namespace application\lib\Validator\Rules\Core;

use application\lib\Validator\Rules\AbstractRule;

/**
 * Not null validation rule.
 *
 * @package Kontrolio\Rules\Core
 */
class NotNull extends AbstractRule
{
    /**
     * Validates input.
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function isValid($input = null)
    {
        return $input !== null;
    }
}
