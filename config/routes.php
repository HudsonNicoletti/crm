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
$router->add("/client/remove/{client:[0-9]+}", [
  'controller' => 'clients',
  'action'     => 'remove',
]);

$router->add("/client/modify/{client:[0-9]+}", [
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

$router->add("/client/update/{client:[0-9]+}", [
  'controller' => 'clients',
  'action'     => 'update',
]);
# END   - ClientsController

# START - ProjectsController
$router->add("/project/{project:[0-9]+}/overview", [
  'controller' => 'projects',
  'action'     => 'overview',
]);

$router->add("/project/{project:[0-9]+}/settings", [
  'controller' => 'projects',
  'action'     => 'settings',
]);

$router->add("/project/{project:[0-9]+}/chart", [
  'controller' => 'projects',
  'action'     => 'chart',
]);

$router->add("/project/{project:[0-9]+}/update", [
  'controller' => 'projects',
  'action'     => 'update',
]);

$router->add("/project/{project:[0-9]+}/remove", [
  'controller' => 'projects',
  'action'     => 'remove',
]);

# Project Members
$router->add("/project/{project:[0-9]+}/members/create", [
  'controller' => 'projectmembers',
  'action'     => 'modal',
  'method'     => 'create',
]);

$router->add("/project/{project:[0-9]+}/members/modify/{member:[0-9]+}", [
  'controller' => 'projectmembers',
  'action'     => 'modal',
  'method'     => 'modify',
]);

$router->add("/project/{project:[0-9]+}/members/remove/{member:[0-9]+}", [
  'controller' => 'projectmembers',
  'action'     => 'modal',
  'method'     => 'remove',
]);

$router->add("/project/{project:[0-9]+}/members/new", [
  'controller' => 'projectmembers',
  'action'     => 'new',
])->via(["POST"]);

$router->add("/project/{project:[0-9]+}/members/update/{member:[0-9]+}", [
  'controller' => 'projectmembers',
  'action'     => 'update',
])->via(["POST"]);

$router->add("/project/{project:[0-9]+}/members/delete/{delete:[0-9]+}", [
  'controller' => 'projectmembers',
  'action'     => 'delete',
])->via(["POST"]);


# Project Tasks
$router->add("/project/{project:[0-9]+}/tasks", [
  'controller' => 'projecttasks',
  'action'     => 'index',
]);

$router->add("/project/{project:[0-9]+}/tasks/create", [
  'controller' => 'projecttasks',
  'action'     => 'modal',
  'method'     => 'create',
]);

$router->add("/project/{project:[0-9]+}/tasks/view/{task:[0-9]+}", [
  'controller' => 'projecttasks',
  'action'     => 'modal',
  'method'     => 'view',
]);

$router->add("/project/{project:[0-9]+}/tasks/modify/{task:[0-9]+}", [
  'controller' => 'projecttasks',
  'action'     => 'modal',
  'method'     => 'modify',
]);

$router->add("/project/{project:[0-9]+}/tasks/new", [
  'controller' => 'projecttasks',
  'action'     => 'new',
])->via(["POST"]);

$router->add("/project/{project:[0-9]+}/tasks/update/{task:[0-9]+}", [
  'controller' => 'projecttasks',
  'action'     => 'update',
])->via(["POST"]);

$router->add("/project/{project:[0-9]+}/tasks/remove/{task:[0-9]+}", [
  'controller' => 'projecttasks',
  'action'     => 'remove',
])->via(["POST"]);

# Project Categories
$router->add("/project/categories", [
  'controller' => 'projectcategories',
  'action'     => 'index',
]);

$router->add("/project/categories/create", [
  'controller' => 'projectcategories',
  'action'     => 'modal',
  'method'     => 'create',
]);

$router->add("/project/categories/modify/{category:[0-9]+}", [
  'controller' => 'projectcategories',
  'action'     => 'modal',
  'method'     => 'modify',
]);

$router->add("/project/categories/remove/{category:[0-9]+}", [
  'controller' => 'projectcategories',
  'action'     => 'modal',
  'method'     => 'remove',
]);

$router->add("/project/categories/new", [
  'controller' => 'projectcategories',
  'action'     => 'new',
])->via(["POST"]);

$router->add("/project/categories/update/{category:[0-9]+}", [
  'controller' => 'projectcategories',
  'action'     => 'update',
])->via(["POST"]);

$router->add("/project/categories/delete/{category:[0-9]+}", [
  'controller' => 'projectcategories',
  'action'     => 'delete',
])->via(["POST"]);
# END   - ProjectsController






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
