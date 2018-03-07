<?php
/**
 * Created by PhpStorm.
 * User: darryldecode
 * Date: 3/7/2018
 * Time: 9:48 AM
 */

namespace App\Components\Core\Menu;


use App\Components\User\Models\User;

class MenuManager
{
    /**
     * the array of MenuItem objects
     *
     * @var array
     */
    protected $menuItems = [];

    /**
     * the current login user object
     *
     * @var
     */
    protected $user;

    /**
     * set the user
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * add menu
     *
     * @param MenuItem $menuItem
     */
    public function addMenu(MenuItem $menuItem)
    {
        $this->menuItems[] = $menuItem;
    }

    /**
     * add menu from given array of MenuItem objects
     *
     * @param array $menus
     */
    public function addMenus(array $menus)
    {
        foreach ($menus as $menu)
        {
            $this->addMenu($menu);
        }
    }

    /**
     * filter the menu based on given user group & permissions
     *
     * @return \Illuminate\Support\Collection|static
     */
    protected function filter()
    {
        $menus = collect($this->menuItems);

        $menus = $menus->filter(function(MenuItem $menu)
        {
            if($menu->isDivider()) return true;

            // set all first to true
            $groupRequirementsPassed = true;
            $permissionRequirementsPassed = true;

            // check group requirements
            if($menu->hasGroupRequirements())
            {
                $groupRequirementsPassed = false;

                foreach ($menu->groupRequirements as $groupName)
                {
                    $groupRequirementsPassed = $this->user->inGroup($groupName);

                    if($groupRequirementsPassed) break;
                }
            }

            // check user requirements
            if($menu->hasPermissionRequirements())
            {
                $permissionRequirementsPassed = false;

                $permissionRequirementsPassed = $this->user->hasAnyPermission($menu->permissionRequirements);
            }

            return $groupRequirementsPassed && $permissionRequirementsPassed;
        });

        return $menus;
    }

    /**
     * get all menus
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll()
    {
        return collect($this->menuItems);
    }

    /**
     * get menus that is filtered already
     *
     * @return MenuManager|\Illuminate\Support\Collection
     */
    public function getFiltered()
    {
        return $this->filter();
    }

    /**
     * check if a the menu items has a given menu label
     *
     * @param string $menuLabel
     * @return bool
     */
    public function hasMenu(string $menuLabel)
    {
        $found = false;
        $menus = $this->filter();

        $menus->each(function(MenuItem $menuItem) use (&$found,$menuLabel)
        {
            if($menuItem->label === $menuLabel) $found = true;
        });

        return $found;
    }
}