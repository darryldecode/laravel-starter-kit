<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/8/2017
 * Time: 5:11 PM
 */

namespace App\Http\Controllers\Front;


use App\User;

class HomeController extends FrontController
{
    public function index()
    {
        return view('welcome');
    }
}