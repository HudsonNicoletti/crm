<?php

namespace Manager\Controllers;

use Manager\Models\Tasks as Tasks,
    Manager\Models\Clients as Clients,
    Manager\Models\Companies as Companies,
    Manager\Models\ProjectTypes as ProjectTypes,
    Manager\Models\Projects as Projects;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\File,
    Phalcon\Forms\Element\Hidden;

use \Phalcon\Mvc\Model\Query\Builder as Builder;

class ProjectsController extends ControllerBase
{

    public function IndexAction()
    {

      $clients = Projects::query()
                ->columns([
                  'Manager\Models\Projects._',
                  'Manager\Models\Projects.title',
                  'Manager\Models\Projects.description',
                  'Manager\Models\Projects.deadline',
                  'Manager\Models\Projects.status',
                  'Manager\Models\Clients.firstname',
                  'Manager\Models\ProjectTypes.title as type',
                ])
                ->where('Manager\Models\Projects.client_type = 1')
                ->innerJoin('Manager\Models\Clients', 'Manager\Models\Clients._ = \Manager\Models\Projects.client')
                ->innerJoin('Manager\Models\ProjectTypes', 'Manager\Models\ProjectTypes._ = Manager\Models\Projects.type')
                ->execute();

      $companies = Projects::query()
                ->columns([
                  'Manager\Models\Projects._',
                  'Manager\Models\Projects.title',
                  'Manager\Models\Projects.description',
                  'Manager\Models\Projects.deadline',
                  'Manager\Models\Projects.status',
                  'Manager\Models\Companies.firstname',
                  'Manager\Models\Companies.fantasy',
                  'Manager\Models\ProjectTypes.title as type',
                ])
                ->where('Manager\Models\Projects.client_type = 2')
                ->innerJoin('Manager\Models\Companies', 'Manager\Models\Companies._ = \Manager\Models\Projects.client')
                ->innerJoin('Manager\Models\ProjectTypes', 'Manager\Models\ProjectTypes._ = Manager\Models\Projects.type')
                ->execute();


      $this->view->companies = $companies;
      $this->view->clients   = $clients;
      $this->view->controller = $this;

    }

    public function CreateAction()
    {
      $form = new Form();

        $form->add(new Hidden( "security" ,[
            'name'  => $this->security->getTokenKey(),
            'value' => $this->security->getToken(),
        ]));

      $this->view->form = $form;
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

      return ($done === 0 ? 0 : round(($done * 100) / $tasks->count()));
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
