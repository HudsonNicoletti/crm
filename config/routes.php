<?php

#   FRONTEND BASIC

$router->add("/", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'index',
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

$router->add("/project/overview/{project:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'overview',
]);

$router->add("/project/tasks/{project:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'tasks',
]);

$router->add("/project/settings/{project:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'settings',
]);

$router->add("/project/chart/{project:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'chart',
]);

$router->add("/project/update/{project:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'update',
]);

$router->add("/project/remove/{project:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'remove',
]);

$router->add("/project/member/new/{project:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'newmember',
]);

$router->add("/project/member/remove/{project:[a-zA-Z0-9\_\-]+}/{member:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'removemember',
]);

$router->add("/project/task/new/{project:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'newtask',
]);

$router->add("/project/task/update/{project:[a-zA-Z0-9\_\-]+}/{task:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'updatetask',
]);

$router->add("/project/task/remove/{project:[a-zA-Z0-9\_\-]+}/{task:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'projects',
    'action'     => 'removetask',
]);

$router->add("/tasks/status/{type:[a-zA-Z0-9\_\-]+}/{task:[a-zA-Z0-9\_\-]+}", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'tasks',
    'action'     => 'status',
]);

$router->add("/settings/email/save", [
    'module'     => 'Manager',
    'namespace'  => 'Manager\Controllers',
    'controller' => 'settings',
    'action'     => 'saveemail',
])->via(["POST"]);

#   FRONTEND - REQUEST API
$router->add("/request/:action", [
    'module'     => 'Manager',
    'controller' => 'request',
    'action'     => 1
])->via(["POST","GET"]);
