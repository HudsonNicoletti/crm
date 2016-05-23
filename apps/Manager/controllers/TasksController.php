<?php

namespace Manager\Controllers;

use Manager\Models\Logs as Logs,
    Manager\Models\Projects as Projects,
    Manager\Models\Tasks as Tasks;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Hidden;

class TasksController extends ControllerBase
{

  private $flags = [
    'status'    => true,
    'title'     => false,
    'text'      => false,
    'redirect'  => false,
    'time'      => false
  ];

  public function IndexAction()
  {
    $tasks = Tasks::query()
    ->columns([
      'Manager\Models\Tasks._',
      'Manager\Models\Tasks.title',
      'Manager\Models\Tasks.description',
      'Manager\Models\Tasks.deadline',
      'Manager\Models\Tasks.completed',
      'Manager\Models\Tasks.status',
      'Manager\Models\Projects.title as project',
    ])
    ->leftJoin('Manager\Models\Projects', 'Manager\Models\Tasks.project = Manager\Models\Projects._')
    ->where("assigned = '{$this->session->get('secure_id')}'")
    ->execute();

    $form = new Form();
    $form->add(new Hidden( "security" ,[
        'name'  => $this->security->getTokenKey(),
        'value' => $this->security->getToken(),
    ]));

    $this->view->form = $form;
    $this->view->tasks = $tasks;
  }

}
