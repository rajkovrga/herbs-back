<?php


namespace App\Exceptions;


class TokenNotFoundException extends \Exception
{

    public function __construct($message = "Token not created", $code = 401)
    {
        parent::__construct($message, $code);
    }
}
