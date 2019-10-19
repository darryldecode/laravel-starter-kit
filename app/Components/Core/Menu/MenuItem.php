<?php
/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 3/7/2018
 * Time: 9:49 AM
 */

namespace App\Components\Core\Menu;


use Illuminate\Support\Arr;

class MenuItem
{
    /**
     * the navigation routing types, whether the nav uses the vue routing or
     * laravel routing
     *
     * @var string
     */
    public static $ROUTING_TYPE_LARAVEL = "laravel";
    public static $ROUTING_TYPE_VUE = "vue";

    /**
     * the navigation types, whether it is a nav link or a divider
     *
     * @var string
     */
    public static $NAV_TYPE_NAV = "nav";
    public static $NAV_TYPE_DIVIDER = "divider";

    /**
     * the array of group names that is required for this
     * menu item, if empty means no group is required
     *
     * @var array
     */
    public $groupRequirements = [];

    /**
     * the array of permission key that is required for this
     * menu item, if empty means no permission is required
     *
     * @var array
     */
    public $permissionRequirements = [];

    /**
     * the label of the menu
     *
     * @var string
     */
    public $label = '';

    /**
     * the navigation type
     *
     * @var string
     */
    public $navType = '';

    /**
     * the navigation icon. See https://material.io/icons/
     *
     * @var string
     */
    public $icon = '';

    /**
     * the routing type (laravel || vue)
     *
     * @var string
     */
    public $routeType = '';

    /**
     * the route name defined in laravel routes if this is a route type laravel
     * or vue-router route name if this is a route type vue.
     *
     * @var string
     */
    public $routeName = '';

    /**
     * if the menu item is visible
     *
     * @var bool
     */
    public $visible = true;

    /**
     * MenuItem constructor.
     * @param array $menuData
     */
    public function __construct(array $menuData = [])
    {
        if(!empty($menuData))
        {
            if($menuData['nav_type'] === self::$NAV_TYPE_NAV)
            {
                $this->groupRequirements = $menuData['group_requirements'];
                $this->permissionRequirements = $menuData['permission_requirements'];
                $this->label = $menuData['label'];
                $this->navType = $menuData['nav_type'];
                $this->icon = $menuData['icon'];
                $this->routeType = $menuData['route_type'];
                $this->routeName = $menuData['route_name'];
                $this->visible = $menuData['visible'] ?? true;
            }
            else
            {
                $this->navType = self::$NAV_TYPE_DIVIDER;
            }
        }
    }

    /**
     * check if the group requirements has required the given group name
     *
     * @param string $groupName
     * @return bool
     */
    public function groupRequirementsHas(string $groupName):bool
    {
        return Arr::get($this->groupRequirements,$groupName);
    }

    /**
     * check if the permission requirements has required the given permission key
     *
     * @param string $permissionKey
     * @return bool
     */
    public function permissionRequirementsHas(string $permissionKey):bool
    {
        return Arr::get($this->permissionRequirements,$permissionKey);
    }

    /**
     * check if this menu item is a divider
     *
     * @return bool
     */
    public function isDivider():bool
    {
        return $this->navType === self::$NAV_TYPE_DIVIDER;
    }

    /**
     * check if it has a group requirements
     *
     * @return bool
     */
    public function hasGroupRequirements()
    {
        return !empty($this->groupRequirements);
    }

    /**
     * check if it has a permission requirements
     *
     * @return bool
     */
    public function hasPermissionRequirements()
    {
        return !empty($this->permissionRequirements);
    }
}