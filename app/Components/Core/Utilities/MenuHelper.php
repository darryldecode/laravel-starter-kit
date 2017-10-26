<?php
/**
 * Created by PhpStorm.
 * User: darryl
 * Date: 10/26/2017
 * Time: 8:09 AM
 */

namespace App\Components\Core\Utilities;


use App\Components\User\Models\User;
use Illuminate\Support\Collection;

class MenuHelper
{
    public static function initMenu()
    {
        /**
         * @var User $currentUser
         */
        $currentUser = \Auth::user();
        $menus = new Collection(config('wask.menu',[]));

        $menus = $menus->filter(function($menu) use (&$currentUser)
        {
            if($menu['nav_type']=='divider') return true;

            if(empty($menu['permission_requirements'])) return true;

            return $currentUser->hasAnyPermission($menu['permission_requirements']);
        });

        view()->share('nav',$menus);
    }
}