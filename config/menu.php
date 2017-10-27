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
 * <route_name> is the vue routers route name
 *
 * <permission_requirements> is array of permissions it requires. Example: user.create, user.update or whatever it is defined on your permissions.
 * if the user does not have the requirements defined on that menu, it will not show to them.
 */
return [
    [
        'permission_requirements' => ['superuser'],
        'label'=>'Dashboard',
        'nav_type' => 'nav',
        'icon'=>'dashboard',
        'route_type'=>'vue',
        'route_name'=>'dashboard'
    ],
    [
        'permission_requirements' => ['superuser'],
        'label'=>'User',
        'nav_type' => 'nav',
        'icon'=>'person',
        'route_type'=>'vue',
        'route_name'=>'users'
    ],
    [
        'permission_requirements' => ['superuser'],
        'label'=>'Files',
        'nav_type' => 'nav',
        'icon'=>'cloud_circle',
        'route_type'=>'vue',
        'route_name'=>'files'
    ],
    [
        'permission_requirements' => [],
        'label'=>'Settings',
        'nav_type' => 'nav',
        'icon'=>'settings',
        'route_type'=>'vue',
        'route_name'=>'settings'
    ],
    [
        'nav_type' => 'divider'
    ]
];