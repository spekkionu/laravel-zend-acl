Laravel Zend Acl
================

[![Latest Stable Version](https://poser.pugx.org/spekkionu/laravel-zend-acl/v/stable.png)](https://packagist.org/packages/spekkionu/laravel-zend-acl)
[![Total Downloads](https://poser.pugx.org/spekkionu/laravel-zend-acl/downloads.png)](https://packagist.org/packages/spekkionu/laravel-zend-acl)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/spekkionu/laravel-zend-acl/badges/quality-score.png?s=40c132d7e25a2856b833195b3e881463c04e07d9)](https://scrutinizer-ci.com/g/spekkionu/laravel-zend-acl/)
[![Code Coverage](https://scrutinizer-ci.com/g/spekkionu/laravel-zend-acl/badges/coverage.png?s=cac2d309c0f9a54c75efc182ab3ba03e16605b1b)](https://scrutinizer-ci.com/g/spekkionu/laravel-zend-acl/)


Adds ACL to Laravel 5 via Zend\Permissions\Acl component.

Most of the ACL solutions for Laravel store the permissions rules in the database or other persistance layer.
This is great if access is dynamic but for applications with set permissions by roles this makes modification more difficult.
Adding new resources, permissions, or roles requires runnning db queries via a migration or other means.
With this package the permissions are stored in code and thus in version control (hopefully).

Rather than reinvent the wheel this package makes use of the Acl package from Zend Framework.
Documentation for the Zend\Permissions\Acl can be found at http://framework.zend.com/manual/2.2/en/modules/zend.permissions.acl.intro.html

## Installation

Add the following line to the `require` section of `composer.json`:

```json
{
    "require": {
        "spekkionu/laravel-zend-acl": "2.*"
    }
}
```
## Setup

1. Add `'Spekkionu\ZendAcl\ZendAclServiceProvider',` to the service provider list in `config/app.php`.
2. Add `'Acl' => 'Spekkionu\ZendAcl\Facades\Acl',` to the list of aliases in `config/app.php`.
3. Run `php artisan vendor:publish --provider=Spekkionu\ZendAcl\ZendAclServiceProvider`

After publishing the permissions will be defined in `app/Http/acl.php`.

## Usage

The Zend\Permissions\Acl is available through the Facade Acl or through the acl service in the IOC container.
The IOC container can also inject the acl instance by type-hinting Zend\Permissions\Acl\Acl.

The permissions can be modified at `app/Http/acl.php`.

### Adding a Resource

You can add a new resource using the addResource method.

```php
<?php
// Add using string shortcut
$acl->addResource('page');
// Add using instance of the Resource class
$acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource('someResource'));
?>
```

### Adding a Role

You can add a new resource using the addRole method.

```php
<?php
// Add using string shortcut
$acl->addRole('admin');
// Add using instance of the Role class
$acl->addRole(new \Zend\Permissions\Acl\Role\GenericRole('member'));
?>
```

### Adding / Removing Permissions

You can add permissions using the allow method.

```php
<?php
// Add page resource
$acl->addResource('page');
// Add admin role
$acl->addRole('admin');
// Add guest role
$acl->addRole('guest');
// Give admin role add, edit, delete, and view permissions for page resource
$acl->allow('admin', 'page', array('add', 'edit', 'delete', 'view'));
// Give guest role only view permissions for page resource
$acl->allow('guest', 'page', 'view');
?>
```
You can remove permissions using the deny method.

```php
<?php
// Add page resource
$acl->addResource('page');
// Add admin role
$acl->addRole('admin');
// Give admin role add, edit, delete, and view permissions for page resource
$acl->allow('admin', 'page', array('add', 'edit', 'delete', 'view'));
// Add staff role that inheirits from admin
$acl->addRole('staff', 'admin');
// Deny access for staff role the delete permission on the page resource
$acl->deny('staff', 'page', 'delete');
?>
```
### Checking for permissions

You can check for access using the isAllowed method

Given the following permissions:

```php
<?php
// Add page resource
Acl::addResource('page');
// Add admin role
Acl::addRole('admin');
// Add guest role
Acl::addRole('guest');
// Give admin role add, edit, delete, and view permissions for page resource
Acl::allow('admin', 'page', array('add', 'edit', 'delete', 'view'));
// Give guest role only view permissions for page resource
Acl::allow('guest', 'page', 'view');
?>
```

```php
<?php
// Check if admin can add page
// Should return true
$allowed = Acl::isAllowed('admin', 'page', 'add');

// Check if admin can delete page
// Should return true
$allowed = Acl::isAllowed('admin', 'page', 'delete');

// Check if guest can edit page
// Should return false
$allowed = Acl::isAllowed('guest', 'page', 'edit');

// Check if guest can view page
// Should return true
$allowed = Acl::isAllowed('guest', 'page', 'view');
?>
```

## Checking permissions for a user

In order to check permissions for a logged in user the user needs to have a field that stores the user's role.
If using an Eloquent user model have the user model implement Zend\Permissions\Acl\Role\RoleInterface.
This interface has one method getRoleId() that should return the role for the user.

### Example Model

Say there is a table `users` that has a field `role`
The following model will allow an instance of the User model to be passed to the isAllowed() method.
```php
<?php
use Illuminate\Database\Eloquent\Model;
use Zend\Permissions\Acl\Role\RoleInterface;

class User extends Model implements RoleInterface
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Returns role of the user
     * @return string
     */
    public function getRoleId()
    {
        return $this->role;
    }
}

```
### Using the user model to check permissions

```php
<?php

// Checking if a user has permissions to view an article
$user = User::find(1);
Acl::isAllowed($user, 'article', 'view');

// Checking if the currently logged in user has permissions to edit a blog post
Acl::isAllowed(Auth::user(), 'post', 'edit');
```

### Adding permission checks to routes

There is an acl route filter included in this package that lets you restrict access by route.
The route filter requires the model returned by Auth::user() to implement `Zend\Permissions\Acl\Role\RoleInterface` as above.

You can add the filter to any route as a before filter such as the following.

```php

Route::get('article/{id}', ['before' => ['acl:article,view'], 'uses' => 'ArticleController@show']);
```

When the route is requested it will check if the currently logged in user has is allowed the view privilege on the article resource.
If there is no logged in user (Auth::guest() returns true) the role `guest` will be checked.

If the user has access to the given resource then the controller will be called as normal.

What happens of the user is not allowed access to the resource is configurable in `config/zendacl.php`.

There are two different actions that can be taken if the user is not authorized.

1. The user can be redirected to a url or named route.
    This can be set by setting the `action` setting to "redirect" or "route" and the `redirect` setting to the url or named route.

2. A view can be rendered.
    This can be set by setting the `action` to "view".
    The `view` setting controls the view to be rendered.
    By default the view will be located at `resources/vendor/zendacl/unauthorized`.
    This view can be modified or the `view` setting can be changed to another view.

Ajax requests will be sent a 401 response regardless of the settings.