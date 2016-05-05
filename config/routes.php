<?php

#   FRONTEND BASIC

$router->add("/", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'index',
    'action'     => 'auth',
]);

$router->add("/:controller", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 1,
]);

$router->add("/:controller/:action", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 1,
    'action'     => 2,
]);

$router->add("/:controller/:action/:params", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 1,
    'action'     => 2,
    'params'     => 3,
]);


$router->add("/clients/remove/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'clients',
    'action'     => 'remove',
]);

$router->add("/clients/modify/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'clients',
    'action'     => 'modify',
]);

$router->add("/clients/new/{type:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'clients',
    'action'     => 'new',
]);

$router->add("/clients/update/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'clients',
    'action'     => 'update',
]);

$router->add("/team/remove/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'team',
    'action'     => 'remove',
]);

$router->add("/team/modify/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'team',
    'action'     => 'modify',
]);

$router->add("/team/update/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'team',
    'action'     => 'update',
]);

$router->add("/team/departments/new", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'team',
    'action'     => 'newdepartment',
]);

$router->add("/team/departments/update/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'team',
    'action'     => 'updatedepartment',
]);

$router->add("/team/departments/remove/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'team',
    'action'     => 'removedepartment',
]);

$router->add("/projects/modify/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'modify',
]);

$router->add("/projects/chart/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'chart',
]);

$router->add("/projects/remove/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'remove',
]);

$router->add("/projects/addmembers/{urlrequest:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'addmembers',
]);

$router->add("/projects/removemember/{project:[a-zA-Z0-9\_\-]+}/{member:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'removemember',
]);

#   FRONTEND - REQUEST API

$router->add("/request/:action", [
    'module'     => 'Manager',
    'controller' => 'request',
    'action'     => 1
])->via(["POST","GET"]);
