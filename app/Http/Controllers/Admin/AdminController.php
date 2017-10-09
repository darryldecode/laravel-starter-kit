<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/5/2017
 * Time: 6:12 AM
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

abstract class AdminController extends Controller
{
    public function __construct()
    {
        view()->share('nav',$this->getMainNav());
    }

    /**
     * get the main navigation
     *
     * @return array
     */
    protected function getMainNav()
    {
        return config('wask.menu',[]);
    }
}