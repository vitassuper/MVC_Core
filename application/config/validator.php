<?php
return [
    'date' => application\lib\Validator\Rules\Core\Date::class,
    'email' => application\lib\Validator\Rules\Core\Email::class,
    'greater_than' => application\lib\Validator\Rules\Core\GreaterThan::class,
    'greater_than_or_equal' => application\lib\Validator\Rules\Core\GreaterThanOrEqual::class,
    'is_null' => application\lib\Validator\Rules\Core\IsNull::class,
    'length' => application\lib\Validator\Rules\Core\Length::class,
    'less_than' => application\lib\Validator\Rules\Core\LessThan::class,
    'less_than_or_equal' => application\lib\Validator\Rules\Core\LessThanOrEqual::class,
    'not_empty' => application\lib\Validator\Rules\Core\NotEmpty::class,
    'not_null' => application\lib\Validator\Rules\Core\NotNull::class,
    'url' => application\lib\Validator\Rules\Core\Url::class
];