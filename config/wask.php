<?php

return [

    /**
     * Dashboard Main Menu
     *
     * label is the title on menu
     * icon is the material icon. See https://material.io/icons/
     * route_type only accepts one value for now 'vue'
     * route_name is the vue routers route name
     */
    'menu' => [
        ['label'=>'Dashboard', 'icon'=>'dashboard', 'route_type'=>'vue', 'route_name'=>'dashboard'],
        ['label'=>'User', 'icon'=>'person', 'route_type'=>'vue', 'route_name'=>'users'],
        ['label'=>'Files', 'icon'=>'cloud_circle', 'route_type'=>'vue', 'route_name'=>'files'],
        ['label'=>'Settings', 'icon'=>'settings', 'route_type'=>'vue', 'route_name'=>'settings'],
    ]
];