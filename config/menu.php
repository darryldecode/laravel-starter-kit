<?php

/**
 * Dashboard Main Menu
 *
 * <label> is the title on menu
 *
 * <icon> is the material icon. See https://material.io/icons/
 *
 * <nav_type> the navigation type 'nav' or 'divider'
 *
 * <route_type> only accepts one value for now 'vue'
 *
 * <route_name> is the vue routers route name. You will be defining vue route names on this file resources/assets/js/router/index.js
 * then use those route names here.
 *
 * <permission_requirements> is array of permissions key it requires. Example: user.create, user.update or whatever it is defined on your permissions.
 * if the current logged in user does not have the requirements defined on that menu item, it will not show to them.
 *
 * <group_requirements> is array of group name it requires. group requirements supersedes permission requirements. So if the user have permission but
 * don't belong to the require groups, menu will not show
 */

use App\Components\Core\Menu\MenuItem;

return [
    new MenuItem([
        'group_requirements' => [],
        'permission_requirements' => ['superuser'],
        'label'=>'Dashboard',
        'nav_type' => MenuItem::$NAV_TYPE_NAV,
        'icon'=>'dashboard',
        'route_type'=>'vue',
        'route_name'=>'dashboard'
    ]),
    new MenuItem([
        'group_requirements' => [],
        'permission_requirements' => ['superuser'],
        'label'=>'User',
        'nav_type' => MenuItem::$NAV_TYPE_NAV,
        'icon'=>'person',
        'route_type'=>'vue',
        'route_name'=>'users'
    ]),
    new MenuItem([
        'group_requirements' => [],
        'permission_requirements' => ['superuser'],
        'label'=>'Files',
        'nav_type' => MenuItem::$NAV_TYPE_NAV,
        'icon'=>'cloud_circle',
        'route_type'=>'vue',
        'route_name'=>'files'
    ]),
    new MenuItem([
        'group_requirements' => [],
        'permission_requirements' => ['superuser'],
        'label'=>'Settings',
        'nav_type' => MenuItem::$NAV_TYPE_NAV,
        'icon'=>'settings',
        'route_type'=>'vue',
        'route_name'=>'settings'
    ]),
    new MenuItem([
        'nav_type' => MenuItem::$NAV_TYPE_DIVIDER
    ])
];