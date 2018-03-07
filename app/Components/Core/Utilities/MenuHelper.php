<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/26/2017
 * Time: 8:09 AM
 */

namespace App\Components\Core\Utilities;


use App\Components\Core\Menu\MenuManager;
use App\Components\User\Models\User;

class MenuHelper
{
    public static function initMenu()
    {
        /**
         * @var User $currentUser
         */
        $currentUser = \Auth::user();
        $menus = config('menu',[]);
        $menuManager = new MenuManager();
        $menuManager->setUser($currentUser);
        $menuManager->addMenus($menus);

        $menus = $menuManager->getFiltered();

        view()->share('nav',$menus);
    }
}