<?php

namespace application\lib\Validator\Rules\Core;

use application\lib\Validator\Rules\AbstractRule;


class Email extends AbstractRule
{
    
    private $mx;

    private $host;

    public function __construct($mx = false, $host = false)
    {
        $this->mx = $mx;
        $this->host = $host;
    }

    public function isValid($input = null)
    {
        if (!preg_match('/^.+\@\S+\.\S+$/', $input)) {
            return false;
        }

        $host = substr($input, strrpos($input, '@') + 1);

        if ($this->mx && !$this->checkMX($host)) {
            $this->violations[] = 'mx';
        }

        if ($this->host && !$this->checkHost($host)) {
            $this->violations[] = 'host';
        }

        return !$this->hasViolations();
    }

    private function checkMX($host)
    {
        return checkdnsrr($host, 'MX');
    }

    private function checkHost($host)
    {
        return $this->checkMX($host) || (checkdnsrr($host, 'A') || checkdnsrr($host, 'AAAA'));
    }
}
