<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/5/2017
 * Time: 6:12 AM
 */

namespace App\Http\Controllers\Admin;


use App\Components\Core\Utilities\MenuHelper;

class HomeController extends AdminController
{
    /**
     * display the admin home page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showHome()
    {
        // so we can access $nav on view
        MenuHelper::initMenu();

        return view('admin.single-page');
    }
}