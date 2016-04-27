<?php

namespace Manager\Controllers;

use Manager\Models\Tasks as Tasks;

use \Phalcon\Mvc\Model\Query\Builder as Builder;

class ProjectsController extends ControllerBase
{

    public function IndexAction()
    {

      $clients = new Builder([
         'models'     => ['Manager\Models\Projects'],
         'columns'    => [
           'Manager\Models\Projects._',
           'Manager\Models\Projects.title',
           'Manager\Models\Projects.description',
           'Manager\Models\Projects.deadline',
           'Manager\Models\Projects.status',
           'Manager\Models\Clients.firstname'
         ],
      ]);
      $clients->where('client_type = 1');
      $clients->innerJoin('Manager\Models\Clients',   'Manager\Models\Clients._ = Manager\Models\Projects.client');
      $clients = $this->modelsManager->executeQuery($clients->getPhql());

      $companies = new Builder([
         'models'     => ['Manager\Models\Projects'],
         'columns'    => [
           'Manager\Models\Projects._',
           'Manager\Models\Projects.title',
           'Manager\Models\Projects.description',
           'Manager\Models\Projects.deadline',
           'Manager\Models\Projects.status',
           'Manager\Models\Companies.firstname',
           'Manager\Models\Companies.fantasy'
         ],
      ]);
      $companies->innerJoin('Manager\Models\Companies', 'Manager\Models\Companies._ = Manager\Models\Projects.client');
      $companies->where('client_type = 2');
      $companies = $this->modelsManager->executeQuery($companies->getPhql());

      $this->view->companies = $companies;
      $this->view->clients   = $clients;
      $this->view->controller = $this;

    }

    public function TaskPercentage($project)
    {
      $done = 0;
      $tasks = Tasks::findByProject($project);

      foreach($tasks as $task)
      {
        if($task->status == 2)
        {
          $done = $done + 1;
        }
      }

      return ($done * 100) / $tasks->count();
    }

    public function ProjectStatus($status)
    {
      switch ($status) {
        case 1 : return 'Aberto'; break;
        case 2 : return 'Concluído'; break;
        case 3 : return 'Cancelado'; break;
      }
    }

}
