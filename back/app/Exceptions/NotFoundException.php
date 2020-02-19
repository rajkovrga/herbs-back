<?php


namespace App\Exceptions;

class NotFoundException extends \Exception
{
    public function __construct($message = 'Not found', $code = 404)
    {
        parent::__construct($message,$code);
    }
}
