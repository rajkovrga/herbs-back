<?php


namespace App\Exceptions;

class NotRemovedException extends \Exception
{
 public function __construct($message = "Row is not deleted", $code = 400)
 {
     parent::__construct($message, $code);
 }
}
