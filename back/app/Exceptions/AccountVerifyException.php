<?php


namespace App\Exceptions;


class AccountVerifyException extends \Exception
{
    public function __construct($message = 'Account verified', $code = 405)
    {
        parent::__construct($message,$code);
    }
}
