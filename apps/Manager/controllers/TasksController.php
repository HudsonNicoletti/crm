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

  public function StatusAction()
  {
    $this->response->setContentType("application/json");

    if(!$this->request->isPost()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao atualizar dados!";
      $this->flags['text']   = "Metodo Inválido.";
    endif;

    if(!$this->security->checkToken()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao atualizar dados!!";
      $this->flags['text']   = "Token de segurança inválido.";
    endif;

    if($this->flags['status']):

      $task = Tasks::findFirst( $this->dispatcher->getParam("task") );

      if( $this->dispatcher->getParam("type") == "close" )
      {
        $task->status = 2;
        $task->completed = (new \DateTime())->format("Y-m-d H:i:s");

        $this->flags['title']  = "Tarefa Concluída!";
        $this->flags['text']   = "Tarefa foi concluída e homologada.";
        $description = "Concluiu a tarefa ({$task->title}).";
      }
      else
      {
        $task->status = 1;
        $task->completed = null;
        
        $this->flags['title']  = "Tarefa Aberta!";
        $this->flags['text']   = "Tarefa foi aberta e homologada.";
        $description = "Abriu a tarefa ({$task->title}).";
      }
      $task->save();

      # Log What Happend
      $this->logManager($this->logs->update,$description,$task->project);

      $this->flags['redirect'] = "/tasks";
      $this->flags['time'] = 0;

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $this->flags['status'],
      "title"     =>  $this->flags['title'],
      "text"      =>  $this->flags['text'],
      "redirect"  =>  $this->flags['redirect'],
      "time"      =>  $this->flags['time']
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
  }
}
