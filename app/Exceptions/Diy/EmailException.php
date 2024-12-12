<?php


namespace App\Exceptions\Diy;


class EmailException extends \Exception
{
    function __construct($msg='')
    {
        parent::__construct($msg);
    }
}
