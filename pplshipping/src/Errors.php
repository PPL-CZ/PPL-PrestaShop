<?php
namespace PPLShipping;

use PPLShipping\Validator\WP_Error;

class Errors extends WP_Error
{
    public function add($code, $message, $data = '')
    {
        parent::add(ltrim($code, "."), $message, $data);
    }
}