<?php

namespace Manager\Controllers;

use Manager\Models\Logs         as Logs,
    Manager\Models\Team         as Team,
    Manager\Models\Tasks        as Tasks,
    Manager\Models\Assignments  as Assignments,
    Manager\Models\ProjectTypes as ProjectTypes,
    Manager\Models\Projects     as Projects;

use Mustache_Engine as Mustache;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Textarea,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\File,
    Phalcon\Forms\Element\Hidden;

use Phalcon\Mvc\Model\Query\Builder as Builder;

class ProjectTasksController extends ControllerBase
{
  private $flags = [
    'status'    => true,
    'title'     => false,
    'text'      => false,
    'redirect'  => false,
    'time'      => false,
    'target'    => false
  ];

  public function IndexAction()
  {
    $this->assets
    ->addCss("assets/manager/css/app/email.css")
    ->addCss('assets/manager/css/plugins/bootstrap-chosen/chosen.css')
    ->addJs("assets/manager/js/plugins/jquery.filtr.min.js")
    ->addJs('assets/manager/js/plugins/inputmask/jquery.inputmask.bundle.js')
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js")
    ->addJs("assets/manager/js/plugins/bootstrap-chosen/chosen.jquery.js");

    $form = new Form();
    $project = $this->dispatcher->getParam("project");

    # Project Task Query
    $tasks = Tasks::query()
    ->columns([
      'Manager\Models\Tasks._',
      'Manager\Models\Tasks.title',
      'Manager\Models\Tasks.description',
      'Manager\Models\Tasks.created',
      'Manager\Models\Tasks.deadline',
      'Manager\Models\Tasks.status',
      'Manager\Models\Team.image',
      'Manager\Models\Team.name'
    ])
    ->innerJoin('Manager\Models\Team', 'Manager\Models\Team.uid = Manager\Models\Tasks.assigned')
    ->where("Manager\Models\Tasks.project = :project:")
    ->bind([
      "project" =>  $project
    ])
    ->orderBy("status ASC , created DESC")
    ->execute();

    $element['security'] = new Hidden( "security" ,[
      'name'  => $this->security->getTokenKey(),
      'value' => $this->security->getToken(),
    ]);
    foreach($element as $e)
    {
      $form->add($e);
    }

    $this->view->form = $form;
    $this->view->tasks = $tasks;
    $this->view->project = Projects::findFirst($project);
    $this->view->pick("projects/tasks");

  }

  public function NewAction()
  {
    $this->response->setContentType("application/json");

    $this->flags['target']  = "#createBox";

    if(!$this->request->isPost()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro!";
      $this->flags['text']   = "Metodo Inválido.";
    endif;

    if(!$this->security->checkToken()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Cadastrar!";
      $this->flags['text']   = "Token de segurança inválido.";
    endif;

    if($this->flags['status']):

      $project = $this->dispatcher->getParam("project");
      $title = $this->request->getPost("title");

      $task = new Tasks;
        $task->project      = $project;
        $task->title        = $title;
        $task->description  = $this->request->getPost("description");
        $task->deadline     = (new \DateTime($this->request->getPost("deadline")))->format("Y-m-d H:i:s");
        $task->created      = (new \DateTime())->format("Y-m-d H:i:s");
        $task->assigned     = $this->request->getPost("assigned");
        $task->status       = 1;
      $task->save();

      $name = Projects::findFirst($project)->title;
      # Log What Happend
      $this->logManager($this->logs->create,"Cadastrou uma nova tarefa ( {$title} )",$project);

      $this->flags['status']     = true ;
      $this->flags['title']      = "Adicionado Com Sucesso!";
      $this->flags['text']       = "Tarefa adicionada com sucesso ao projeto!";
      $this->flags['redirect']   = "/project/{$project}/tasks";
      $this->flags['time']       = 1200;

    endif;

    return $this->response->setJsonContent([
      "status"    => $this->flags['status'] ,
      "title"     => $this->flags['title'] ,
      "text"      => $this->flags['text'] ,
      "redirect"  => $this->flags['redirect'] ,
      "time"      => $this->flags['time'],
      "target"    => $this->flags['target'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
  }

  public function UpdateAction()
  {
    $this->response->setContentType("application/json");

    $this->flags['target']  = "#updateBox";

    if(!$this->request->isPost()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro!";
      $this->flags['text']   = "Metodo Inválido.";
    endif;

    if(!$this->security->checkToken()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Cadastrar!";
      $this->flags['text']   = "Token de segurança inválido.";
    endif;

    if($this->flags['status']):

      $title = $this->request->getPost("title");
      $project = $this->dispatcher->getParam("project");
      $task = $this->dispatcher->getParam("task");

      $task = Tasks::findFirst($task);
        $task->title        = $title;
        $task->description  = $this->request->getPost("description");
        $task->deadline     = (new \DateTime($this->request->getPost("deadline")))->format("Y-m-d H:i:s");
        $task->assigned     = $this->request->getPost("assigned");
      $task->save();

      $name = Projects::findFirst($project)->title;
      # Log What Happend
      $this->logManager($this->logs->update,"Alterou informações da tarefa ( {$title} )",$project);

      $this->flags['status']     = true ;
      $this->flags['title']      = "Alterado Com Sucesso!";
      $this->flags['text']       = "Tarefa alterada com sucesso!";
      $this->flags['redirect']   = "/project/{$project}/tasks";
      $this->flags['time']       = 1200;

    endif;

    return $this->response->setJsonContent([
      "status"    => $this->flags['status'] ,
      "title"     => $this->flags['title'] ,
      "text"      => $this->flags['text'] ,
      "redirect"  => $this->flags['redirect'] ,
      "time"      => $this->flags['time'],
      "target"    => $this->flags['target'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
  }

  public function RemoveAction()
  {
    $this->response->setContentType("application/json");

    if(!$this->request->isPost()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro!";
      $this->flags['text']   = "Metodo Inválido.";
    endif;

    if(!$this->security->checkToken()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Cadastrar!";
      $this->flags['text']   = "Token de segurança inválido.";
    endif;

    if($this->flags['status']):

      $project = $this->dispatcher->getParam("project");
      $task = Tasks::findFirst($this->dispatcher->getParam("task"));
      $name = Projects::findFirst($project)->title;

      # Log What Happend
      $this->logManager($this->logs->delete,"Removeu a tarefa ( {$task->title} )",$project);

      $task->delete();

      $this->flags['status']     = true ;
      $this->flags['title']      = "Removido Com Sucesso!";
      $this->flags['text']       = "Tarefa removida com sucesso do projeto!";
      $this->flags['redirect']   = "/project/{$project}/tasks";
      $this->flags['time']       = 1200;

    endif;

    return $this->response->setJsonContent([
      "status"    => $this->flags['status'] ,
      "title"     => $this->flags['title'] ,
      "text"      => $this->flags['text'],
      "redirect"  => $this->flags['redirect'] ,
      "time"      => $this->flags['time'],
      "target"    => $this->flags['target'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
  }

  public function ModalAction()
  {
    $this->response->setContentType("application/json");

    if(!$this->request->isGet()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Alterar!";
      $this->flags['text']   = "Metodo Inválido.";
    endif;

    if($this->flags['status']):

      $form = new Form();
      $action = false;
      $inputs = [];
      $project = $this->dispatcher->getParam("project");
      $task_id = $this->dispatcher->getParam("task");

      if($task_id)
      {
        $task = Tasks::findFirst($task_id);
      }

      # Assigned Members Query
      $members = Assignments::query()
      ->columns([
        'Manager\Models\Team.uid',
        'Manager\Models\Team.name'
      ])
      ->innerJoin('Manager\Models\Team', 'Manager\Models\Team.uid = Manager\Models\Assignments.member')
      ->where("Manager\Models\Assignments.project = :project:")
      ->bind([
        "project" =>  $project
      ])
      ->execute();
      # Workaround for select
      foreach ($members as $member) {
        $assignedMembers[$member->uid] = $member->name;
      }

      # CREATING ELEMENTS
      $element['title'] = new Text( "title" ,[
        'class'         => "form-control",
        'title'         => "Título",
        'data-validate' => true,
        'data-empty'    => "* Campo Obrigatório"
      ]);

      $element['description'] = new Textarea( "description" ,[
        'class'         => "form-control",
        'title'         => "Observações",
        'placeholder'   => "Breve Descrição ..."
      ]);

      $element['deadline'] = new Text( "deadline" ,[
        'class'         => "form-control inputmask",
        'title'         => "Deadline",
        'data-validate' => true,
        'data-empty'    => "* Campo Obrigatório",
        'placeholder'   => "dd-mm-yyyy",
        'data-inputmask'=> "'alias': 'dd-mm-yyyy'"
      ]);

      $element['assigned'] = new Select( "assigned" , $assignedMembers ,[
        'using' =>  ['uid','name'],
        'title' => "Responsável",
        'class' => "chosen-select form-control"
      ]);

      $element['security'] = new Hidden( "security" ,[
        'name'  => $this->security->getTokenKey(),
        'value' => $this->security->getToken(),
      ]);

      # IF REQUEST IS TO CREATE JUST POPULATE WITH DEFAULT ELEMENTS
      if( $this->dispatcher->getParam("method") == "create" ):
        $action = "/project/{$project}/tasks/new";
        $template = "create";
        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO UPDATE POPULATE WITH VALJUE TO ELEMENT
      elseif ($this->dispatcher->getParam("method") == "modify"):
        $action = "/project/{$project}/tasks/update/{$task_id}";
        $template = "modify";

        $element['title']       ->setAttribute("value",$task->title);
        $element['description'] ->setAttribute("value",$task->description);
        $element['deadline']    ->setAttribute("value",(new \DateTime($task->deadline))->format("d-m-Y"));
        $element['assigned']    ->setAttribute("value",$task->assigned);
        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO VIEW POPULATE WITH VALJUE TO ELEMENT
      elseif ($this->dispatcher->getParam("method") == "view"):
        $template = "view";

        $element['title']       ->setAttribute("disabled",true)->setAttribute("value",$task->title);
        $element['description'] ->setAttribute("disabled",true)->setAttribute("value",$task->description);
        $element['deadline']    ->setAttribute("disabled",true)->setAttribute("value",(new \DateTime($task->deadline))->format("d-m-Y"));
        $element['assigned']    ->setAttribute("disabled",true)->setAttribute("value",$task->assigned);
        foreach($element as $e)
        {
          $form->add($e);
        }

      endif;

      # POPULATE ARRAY WITH TITLE AND INPUTS FOR RENDERING
      foreach($form as $f)
      {
        array_push($inputs,[ "title" => $f->getAttribute("title") , "input" => $f->render($f->getName()) ]);
      }

      # RENDER
      $body = (new Mustache)->render(file_get_contents($_SERVER['DOCUMENT_ROOT']."/templates/modal.tpl"),[
        $template => true,
        "action"  => $action,
        "inputs"  => $inputs
      ]);

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $this->flags['status'],
      "data"      =>  [ "#{$template}" , $body ]  # Modal Target , data
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
  }
}
