<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/5/2017
 * Time: 6:12 AM
 */

namespace App\Http\Controllers\Admin;


class HomeController extends AdminController
{
    /**
     * display the admin home page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showHome()
    {
        return view('admin.single-page');
    }
}