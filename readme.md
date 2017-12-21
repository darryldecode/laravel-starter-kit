<img src="https://assets.darrylfernandez.com/wp-content/uploads/2017/12/Screenshot_1.png"></p>
<img src="https://assets.darrylfernandez.com/wp-content/uploads/2017/12/Screenshot_7.png"></p>

## About Laravel Starter Kit

Laravel Starter Kit is based on Laravel 5.5 and VueJS + Material design.

## Features

&#10004; User, Group & Permissions Management

&#10004; Uses the default Laravel Auth + some goodies to handle complex permissions. No worries of learning anything!

&#10004; Comes with File Manager Out of the box

&#10004; Easy to work with, no complex patterns & techniques

&#10004; Everything is explicitly defined so any developer can quickly dive in
to the codes and start working with the starter kit

&#10004; Built in Material Design using <a href="https://vuetifyjs.com/">Vuetify</a>

&#10004; Single Page Admin Dashboard

## DOCUMENTATION

- <a href="#requirements">Requirements</a>
- <a href="#installation">Installation</a>
- <a href="#user-group-permissions">User, Groups & Permissions Guide</a>
- <a href="#dashboard-menu">Adding Dashboard Menu</a>

<h3 id="requirements">Requirements</h3>

- Requirements are same with Laravel 5.5

<h3 id="installation">Installation</h3>

- Clone repository `git clone https://github.com/darryldecode/laravel-starter-kit.git`
- `cp .env.example .env` Then open .env file and put necessary credentials
- `composer install`
- `php artisan key:generate`
- `php artisan migrate`
- `php artisan db:seed`
- `php artisan storage:link`
- `npm install`

Then for developent you can run `npm run dev or watch or prod`

You are done! You can now login in your application using
the following credentials:

- username: admin@gmail.com
- password: 12345678

<h3 id="user-group-permissions">User, Groups & Permissions</h3>

- Get current logged in user or Get User
    - `$User = \Auth::user()`
    - `$User = App\Components\User\Models\User::find($id)`
- Check if user belongs to a group
    - `$User->inGroup($groupId|$groupName|$groupObject);`
- Check if user has given permission
    - `$User->hasPermission('blog.create')`
    - `$User->hasAnyPermission(['blog.create','user.edit','etc'])`
- Check if user is a super user
    - `$User->isSuperUser()`
- Get User's combined permissions. This will include permission acquired from its group and its specific given permissions:
    - `$User->getCombinedPermissions()`
- Primarily, you can assign, re-assign or revoke permissions on a user using the backend UI. But you can also do that programmatically.

```
$User = App\Components\User\Models\User::find($id);

// using permission ID
$permissionID = 1 // the permission ID that you add/define on permissions
$permissionValue = 1 // values can be (1,0,-1) allow=1, inherit=0, deny=-1

$User->addPermission($permissionID,$permissionValue); // true or false

// using permission object
$permission = App\Components\User\Models\Permission::find($id); // the permission object
$permissionValue = 1 // values can be (1,0,-1) allow=1, inherit=0, deny=-1

$User->addPermission($permission,$permissionValue); // true or false

NOTE: if the permission is already exist on the user, it will just update the value.

// remove a permission
$User->removePermission($permissionID|$permissionObject);
```

<h3 id="dashboard-menu">Adding Dashboard Menu</h3>

- Please open `config/menu.php` and see 'menu' array