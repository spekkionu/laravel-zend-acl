<?php

use Laminas\Permissions\Acl\Acl;
use Laminas\Permissions\Acl\Resource\GenericResource;
use Laminas\Permissions\Acl\Role\GenericRole as Role;

/*
|--------------------------------------------------------------------------
| ACL Resources, Roles, and Permissions
|--------------------------------------------------------------------------
|
| Below you may add resources and roles and define the permissions
| roles have on those resources.
|
| The Acl instance is available as $acl
|
*/

/**
 * @var Acl $acl
 */

/*
|--------------------------------------------------------------------------
| ACL Resources
|--------------------------------------------------------------------------
|
| Add your acl resources below.
|
| Examples
| $acl->addResource('page');
| $acl->addResource(new GenericResource('someResource'));
|
*/
$acl->addResource(new GenericResource('guest'));

/*
|--------------------------------------------------------------------------
| ACL Roles
|--------------------------------------------------------------------------
|
| Add your acl roles below.
|
| Examples
| $acl->addRole('member');
| $acl->addRole(new Role('admin'));
|
*/
$acl->addRole(new Role('guest'));

/*
|--------------------------------------------------------------------------
| ACL Permissions
|--------------------------------------------------------------------------
|
| Give roles permissions on resources
|
| Examples
| Give admin role add, edit, delete, and view permissions for page resource
| $acl->allow('admin', 'page', array('add', 'edit', 'delete', 'view'));
|
| // Give member role only view permissions for page resource
| $acl->allow('member', 'page', 'view');
|
*/
$acl->allow('guest', 'guest');
