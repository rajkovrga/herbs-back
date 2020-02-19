<?php


namespace App\Exceptions;

class PasswordNotException extends \Exception
{
    public function __construct($message = 'Password is not valid', $code = 403)
    {
        parent::__construct($message,$code);
    }

}
