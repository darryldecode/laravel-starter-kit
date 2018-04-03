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

    /**
     * format ms to seconds
     *
     * @param $duration
     * @param int $decimal
     * @return string
     */
    public static function formatMillisecondsToSeconds($duration,$decimal = 5)
    {
        $hours = (int)($duration / 60 / 60);
        $minutes = (int)($duration / 60) - $hours * 60;
        $seconds = (float)$duration - $hours * 60 * 60 - $minutes * 60;

        return number_format($seconds,$decimal);
    }

    /**
     * helper to convert camel case to dash format
     *
     * @param $string
     * @param string $separator
     * @return string
     */
    public static function camel2snake($string,$separator = '_') {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', "$1{$separator}", $string));
    }

    /**
     * helper to convert dash to camel case
     *
     * @param $string
     * @param bool $capitalizeFirstCharacter
     * @param string $separator
     * @return mixed|string
     */
    function snake2Camel($string, $capitalizeFirstCharacter = false, $separator = '_')
    {
        $str = str_replace($separator, '', ucwords($string, $separator));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }

    /**
     * check if a string contains a specific char or word
     *
     * @param string $subject
     * @param string $charOrWord
     * @return bool
     */
    public static function stringContains($subject,$charOrWord)
    {
        if (strpos($subject,$charOrWord) !== false) {
            return true;
        }
        return false;
    }

    /**
     * helper to convert comma separated string into an array.
     * if the supplied parameter empty string, it will return an empty array
     *
     * @param $string
     * @return array
     */
    public static function commasToArray(string $string) : array
    {
        if($string==="") return [];

        return explode(",",trim($string,","));
    }

    /**
     * helper to check if the given string has web protocol
     *
     * @param string $string
     * @return bool
     */
    public static function hasWebProtocol(string $string) : bool
    {
        $d = parse_url($string);

        return !empty($d['scheme']);
    }
}