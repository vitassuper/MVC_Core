<?php

namespace application\lib\Validator\Rules\Core;

use application\lib\Validator\Rules\AbstractComparisonRule;

/**
 * Greater than or equal validation rule.
 *
 * @package Kontrolio\Rules\Core
 */
class GreaterThanOrEqual extends AbstractComparisonRule
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
        return $input >= $this->value;
    }
}
