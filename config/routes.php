<?php

# START - IndexController
$router->add("/calenderEvents", [
  'controller' => 'index',
  'action'     => 'calendar',
]);
# END   - IndexController

# START - TasksController
$router->add("/task/create", [
  'controller' => 'tasks',
  'action'     => 'modal',
  'method'     => 'create',
]);

$router->add("/task/view/{task:[0-9]+}", [
  'controller' => 'tasks',
  'action'     => 'modal',
  'method'     => 'view',
]);

$router->add("/task/modify/{task:[0-9]+}", [
  'controller' => 'tasks',
  'action'     => 'modal',
  'method'     => 'modify',
]);

$router->add("/task/new", [
  'controller' => 'tasks',
  'action'     => 'new',
])->via(["POST"]);

$router->add("/task/update/{task:[0-9]+}", [
  'controller' => 'tasks',
  'action'     => 'update',
])->via(["POST"]);

$router->add("/task/close/{task:[0-9]+}", [
  'controller' => 'tasks',
  'action'     => 'status',
  'method'     => 'close',
])->via(["POST"]);

$router->add("/task/open/{task:[0-9]+}", [
  'controller' => 'tasks',
  'action'     => 'status',
  'method'     => 'open',
])->via(["POST"]);
# END   - TasksController

# START - TeamController
$router->add("/team/create", [
  'controller' => 'team',
  'action'     => 'modal',
  'method'     => 'create',
]);

$router->add("/team/view/{member:[0-9]+}", [
  'controller' => 'team',
  'action'     => 'modal',
  'method'     => 'view',
]);

$router->add("/team/modify/{member:[0-9]++}", [
  'controller' => 'team',
  'action'     => 'modal',
  'method'     => 'modify',
]);

$router->add("/team/remove/{member:[0-9]+}", [
  'controller' => 'team',
  'action'     => 'modal',
  'method'     => 'remove',
]);

$router->add("/team/new", [
  'controller' => 'team',
  'action'     => 'new',
])->via(["POST"]);

$router->add("/team/update/{member:[0-9]+}", [
  'controller' => 'team',
  'action'     => 'update',
])->via(["POST"]);

$router->add("/team/delete/{member:[0-9]+}", [
  'controller' => 'team',
  'action'     => 'delete',
])->via(["POST"]);

##  TEAM DEPARMENTS
$router->add("/team/departments", [
  'controller' => 'teamdepartments',
  'action'     => 'index',
]);

$router->add("/team/department/create", [
  'controller' => 'teamdepartments',
  'action'     => 'modal',
  'method'     => 'create',
]);

$router->add("/team/department/modify/{department:[0-9]+}", [
  'controller' => 'teamdepartments',
  'action'     => 'modal',
  'method'     => 'modify',
]);

$router->add("/team/department/remove/{department:[0-9]+}", [
  'controller' => 'teamdepartments',
  'action'     => 'modal',
  'method'     => 'remove',
]);

$router->add("/team/department/new", [
  'controller' => 'teamdepartments',
  'action'     => 'new',
])->via(["POST"]);

$router->add("/team/department/update/{department:[0-9]+}", [
  'controller' => 'teamdepartments',
  'action'     => 'update',
])->via(["POST"]);

$router->add("/team/department/delete/{department:[0-9]+}", [
  'controller' => 'teamdepartments',
  'action'     => 'delete',
])->via(["POST"]);

# END   - TeamController

# START - ClientsController
$router->add("/client/view/{id:[0-9]+}", [
  'controller' => 'clients',
  'action'     => 'modal',
  'method'     => 'view',
]);

$router->add("/client/remove/{id:[0-9]+}", [
  'controller' => 'clients',
  'action'     => 'remove',
]);

$router->add("/client/modify/{id:[0-9]+}", [
  'controller' => 'clients',
  'action'     => 'modify',
]);

$router->add("/clients/new/person", [
  'controller' => 'clients',
  'action'     => 'person',
]);

$router->add("/clients/new/company", [
  'controller' => 'clients',
  'action'     => 'company',
]);

$router->add("/client/update/{id:[0-9]+}", [
  'controller' => 'clients',
  'action'     => 'update',
]);
# END   - ClientsController




$router->add("/projects/new/category", [
  'module'     => 'Manager',
  'namespace'  => 'Manager\Controllers',
  'controller' => 'projects',
  'action'     => 'newcategory',
]);

$router->add("/projects/update/category/{type:[a-zA-Z0-9\_\-]+}", [
  'module'     => 'Manager',
  'namespace'  => 'Manager\Controllers',
  'controller' => 'projects',
  'action'     => 'updatecategory',
]);

$router->add("/projects/remove/category/{type:[a-zA-Z0-9\_\-]+}", [
  'module'     => 'Manager',
  'namespace'  => 'Manager\Controllers',
  'controller' => 'projects',
  'action'     => 'removecategory',
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



$router->add("/tickets/view/{ticket:[a-zA-Z0-9\_\-]+}", [
  'module'     => 'Manager',
  'namespace'  => 'Manager\Controllers',
  'controller' => 'tickets',
  'action'     => 'view',
]);

$router->add("/tickets/send/{ticket:[a-zA-Z0-9\_\-]+}", [
  'module'     => 'Manager',
  'namespace'  => 'Manager\Controllers',
  'controller' => 'tickets',
  'action'     => 'send',
])->via(["POST"]);

$router->add("/tickets/close/{ticket:[a-zA-Z0-9\_\-]+}", [
  'module'     => 'Manager',
  'namespace'  => 'Manager\Controllers',
  'controller' => 'tickets',
  'action'     => 'close',
])->via(["POST"]);

$router->add("/settings/email/save", [
  'module'     => 'Manager',
  'namespace'  => 'Manager\Controllers',
  'controller' => 'settings',
  'action'     => 'saveemail',
])->via(["POST"]);

$router->add("/settings/server/save", [
  'module'     => 'Manager',
  'namespace'  => 'Manager\Controllers',
  'controller' => 'settings',
  'action'     => 'saveserver',
])->via(["POST"]);

$router->add("/settings/admin/add", [
  'module'     => 'Manager',
  'namespace'  => 'Manager\Controllers',
  'controller' => 'settings',
  'action'     => 'addadmin',
])->via(["POST"]);

$router->add("/settings/admin/remove/{uid:[a-zA-Z0-9\_\-]+}", [
  'module'     => 'Manager',
  'namespace'  => 'Manager\Controllers',
  'controller' => 'settings',
  'action'     => 'removeadmin',
])->via(["POST"]);
