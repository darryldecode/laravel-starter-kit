<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 9/29/2017
 * Time: 4:04 AM
 */

namespace App\Components\Core\Utilities;


use Illuminate\Support\Facades\Log;

class Helpers
{
    /**
     *  helper to check if a given variable has value
     *
     * @param $var
     * @param string|null $default
     * @return string
     */
    public static function hasValue(&$var,$default = null)
    {
        try {

            if(!isset($var)) return $default;

            if(is_bool($var)) return $var;

            if(empty($var) || is_null($var)) return $default;

            return $var;

        } catch (\Exception $e) {

            Log::error($e->getMessage());
            return $default;

        }
    }

    /**
     * multi-dimensional array sorting by custom keys
     *
     * @param $array
     * @param string $key
     * @param bool $highestFirst
     */
    public static function sort(&$array,$key = 'sort',$highestFirst = true)
    {
        usort($array, function($a, $b) use ($key,$highestFirst) {
            return ($highestFirst) ? $b[$key] - $a[$key] : $a[$key] - $b[$key];
        });
    }

    /**
     * check if property exist or return a default value if not
     *
     * @param $class
     * @param $propertyName
     * @param string $default
     * @return string
     */
    public static function propertySafe(&$class,$propertyName,$default = '')
    {
        return property_exists($class,$propertyName) ? $class->{$propertyName} : $default;
    }

    /**
     * check if array key exist or return a default value if not
     *
     * @param $array
     * @param $key
     * @param string $default
     * @return string
     */
    public static function arraySafe(&$array,$key,$default = '')
    {
        return isset($array['$key']) ? $array[$key] : $default;
    }

    /**
     * safely use a variable or return a value if the variable throws an error
     *
     * @param $var
     * @param string $default
     * @return string
     */
    public static function trySafe(&$var,$default = '')
    {
        try {
            return $var;
        } catch (\Exception $e)
        {
            return $default;
        }
    }
}