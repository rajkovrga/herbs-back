<?php


namespace App\Exceptions;


class VerifyException extends \Exception
{
    public function __construct($message = 'Account is not verify', $code = 403)
    {
        parent::__construct($message,$code);
    }
}
