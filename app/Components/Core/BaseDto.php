<?php
/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 2/21/2018
 * Time: 8:33 PM
 */

namespace App\Components\Core;

abstract class BaseDto
{
    public static function get()
    {
        return new static();
    }
}