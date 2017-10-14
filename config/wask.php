<?php

return [

    /**
     * Dashboard Main Menu
     *
     * label is the title on menu
     * icon is the material icon. See https://material.io/icons/
     * nav_type the navigation type 'nav' or 'divider'
     * route_type only accepts one value for now 'vue'
     * route_name is the vue routers route name
     */
    'menu' => [
        ['label'=>'Dashboard', 'nav_type' => 'nav', 'icon'=>'dashboard', 'route_type'=>'vue', 'route_name'=>'dashboard'],
        ['label'=>'User', 'nav_type' => 'nav', 'icon'=>'person', 'route_type'=>'vue', 'route_name'=>'users'],
        ['label'=>'Files', 'nav_type' => 'nav', 'icon'=>'cloud_circle', 'route_type'=>'vue', 'route_name'=>'files'],
        ['label'=>'Settings', 'nav_type' => 'nav', 'icon'=>'settings', 'route_type'=>'vue', 'route_name'=>'settings'],
        ['nav_type' => 'divider'],
    ]
];