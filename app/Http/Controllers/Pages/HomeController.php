<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/8/2017
 * Time: 5:11 PM
 */

namespace App\Http\Controllers\Pages;


use App\User;

class HomeController extends PagesController
{
    public function index()
    {
        $User = User::find(2);

        return $User->getCombinedPermissions();
    }
}