<?php

namespace application\exceptions;

class NotFoundException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message ?: 'Не найдено', $code ?: 404, $previous);
    }
}