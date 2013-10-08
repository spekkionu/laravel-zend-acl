Laravel Zend Acl
================

[![Latest Stable Version](https://poser.pugx.org/spekkionu/laravel-zend-acl/v/stable.png)](https://packagist.org/packages/spekkionu/laravel-zend-acl)
[![Total Downloads](https://poser.pugx.org/spekkionu/laravel-zend-acl/downloads.png)](https://packagist.org/packages/spekkionu/laravel-zend-acl)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/spekkionu/laravel-zend-acl/badges/quality-score.png?s=40c132d7e25a2856b833195b3e881463c04e07d9)](https://scrutinizer-ci.com/g/spekkionu/laravel-zend-acl/)
[![Code Coverage](https://scrutinizer-ci.com/g/spekkionu/laravel-zend-acl/badges/coverage.png?s=cac2d309c0f9a54c75efc182ab3ba03e16605b1b)](https://scrutinizer-ci.com/g/spekkionu/laravel-zend-acl/)


Adds ACL to Laravel 4 via Zend\Permissions\Acl component.

Most of the ACL solutions for Laravel 4 store the permissions rules in the database or other persistance layer.
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
        "spekkionu/laravel-zend-acl": "dev-master"
    }
}
```
## Setup

1. Add `Spekkionu\ZendAcl\ZendAclServiceProvider` to the service provider list in `app/config/app.php`.
2. Add `'Acl' => 'Spekkionu\ZendAcl\Facades\Acl',` to the list of aliases in `app/config/app.php`.

## Usage

The Zend\Permissions\Acl is available through the Facade Acl or through the acl service in the IOC container.

### Adding a Resource

You can add a new resource using the addResource method.

```php
<?php
// Add using string shortcut
Acl::addResource('page');
// Add using instance of the Resource class
Acl::addResource(new \Zend\Permissions\Acl\Resource\GenericResource('someResource'));
?>
```

### Adding a Role

You can add a new resource using the addRole method.

```php
<?php
// Add using string shortcut
Acl::addRole('admin');
// Add using instance of the Role class
Acl::addRole(new \Zend\Permissions\Acl\Role\GenericRole('member'));
?>
```

### Adding / Removing Permissions

You can add permissions using the allow method.

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
You can remove permissions using the deny method.

```php
<?php
// Add page resource
Acl::addResource('page');
// Add admin role
Acl::addRole('admin');
// Give admin role add, edit, delete, and view permissions for page resource
Acl::allow('admin', 'page', array('add', 'edit', 'delete', 'view'));
// Add staff role that inheirits from admin
Acl::addRole('staff', 'admin');
// Deny access for staff role the delete permission on the page resource
Acl::deny('staff', 'page', 'delete');
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

## TODO

So far this package just provides access to the Zend Acl class.

I plan on adding some deeper integrations with the Laravel Auth library so it knows the roles of the currently logged in user.
