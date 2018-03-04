[![Build Status](https://travis-ci.org/darryldecode/laravel-starter-kit.svg?branch=master)](https://travis-ci.org/darryldecode/laravel-starter-kit)

<img src="https://assets.darrylfernandez.com/wp-content/uploads/2017/12/Screenshot_1.png"></p>
<img src="https://assets.darrylfernandez.com/wp-content/uploads/2017/12/Screenshot_7.png"></p>

## About Laravel Starter Kit

Laravel Starter Kit is based on Laravel 5.6 and VueJS + Material design(Vuetify).

## Why?

So many starter kits out there but so bloated with features and sometimes an overkill. This tries to provide you the very basic 
that most of the application needs. In most cases, you only need this to go further with still full control on your foundation, no 
magic and extra complexities, just the right sugar.

NOTE: This starter app assumes you have a good fundamental knowledge with Laravel framework and vueJS. If you are new to this
technologies then this app might not be for you. If you have good knowledge already with Laravel & VueJS then things here should be
straight forward to you and you can easily expand the app to your needs.

## Features

&#10004; User, Group & Permissions Management

&#10004; Uses the default Laravel Auth + some goodies to handle complex permissions. No worries of learning anything!

&#10004; Comes with File Manager Out of the box

&#10004; Easy to work with, no complex patterns & techniques

&#10004; Everything is explicitly defined so any developer can quickly dive in
to the codes and start working with the starter kit

&#10004; Built in Material Design using <a href="https://vuetifyjs.com/">Vuetify</a>

&#10004; Single Page Admin Dashboard

## Stack Info
- Laravel v5.6
- VueJS v3+
- Vuetify(Material Design) v1.0.*

## DOCUMENTATION

- <a href="#requirements">Requirements</a>
- <a href="#installation">Installation</a>
- <a href="#compiling-assets">Compiling Assets</a>
- <a href="#user-group-permissions">User, Groups & Permissions Guide</a>
- <a href="#dashboard-menu">Adding Dashboard Menu</a>

<h3 id="requirements">Requirements</h3>

- Requirements are same with Laravel 5.5

<h3 id="installation">Installation</h3>

- Clone repository `git clone https://github.com/darryldecode/laravel-starter-kit.git`
- `cp .env.example .env` Then open .env file and put necessary credentials. Make sure to put proper APP_URL because it will be use in file manager module.
- `composer install`
- `php artisan key:generate`
- `php artisan migrate`
- `php artisan db:seed`
- `php artisan storage:link`
- `npm install`

<h3 id="compiling-assets">Compiling Assets</h3>

Then for **development** you can run `npm run dev`.

Or for **monitor and automatically recompile** `npm run watch`.

Or for **recompile and minify** `npm run prod`.

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
- Primarily, you can assign, re-assign or revoke permissions on a user & group using the backend UI. But you can also do that programmatically.

```php
// Assign, Re-assign, revoke permission in a USER:

$User = App\Components\User\Models\User::find($id);

// using permission ID
$permissionID = 1 // the permission ID that you add/define on permissions
$permissionValue = 1 // values can be (1,0,-1) allow=1, inherit=0, deny=-1

$User->addPermission($permissionID,$permissionValue);

// using permission object
$permission = App\Components\User\Models\Permission::find($id); // the permission object
$permissionValue = 1 // values can be (1,0,-1) allow=1, inherit=0, deny=-1

$User->addPermission($permission,$permissionValue);

NOTE: if the permission is already exist on the user, it will just update the value.

// remove a permission
$User->removePermission($permissionID|$permissionObject);
```

- We can also do the same in a Group

```php
// Assign, Re-assign, revoke permission in a GROUP:

$Group = App\Components\User\Models\Group::find($id);

// using permission ID
$permissionID = 1 // the permission ID that you add/define on permissions
$permissionValue = 1 // values can be (1,-1) allow=1, deny=-1

$Group->addPermission($permissionID,$permissionValue);

// using permission object
$permission = App\Components\User\Models\Permission::find($id); // the permission object
$permissionValue = 1 // values can be (1,-1) allow=1, deny=-1

$Group->addPermission($permission,$permissionValue);

NOTE: if the permission is already exist on the group, it will just update the value.

// remove a permission
$Group->removePermission($permissionID|$permissionObject);
```

<h3 id="dashboard-menu">Adding Dashboard Menu</h3>

- Please open `config/menu.php` and see 'menu' array.
- To give a modern backend with a smooth navigation from page to page, I designed at as a hybrid single page app. The routing in the admin panel is powered by vueJS's vue-router, so you will need first define those routes on the vue-router and make it work
then add that new route to `config/menu.php` to make it appear on menu.

<h3 id="contributing">Contributing</h3>

- Only accepts patches, improvements & bug fixes.
- For pull requests related to additional modules or features, it will be subject for discussion as this project is intended for a very lean starting point and avoids to be bloated.

<h3>Disclaimer</h3>

THIS SOFTWARE IS PROVIDED "AS IS" AND ANY EXPRESSED OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE AUTHOR, OR ANY OF THE CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
