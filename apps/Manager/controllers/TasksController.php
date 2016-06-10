<?php

namespace Manager\Controllers;

use Manager\Models\Logs as Logs,
    Manager\Models\Team as Team,
    Manager\Models\Projects as Projects,
    Manager\Models\Tasks as Tasks;

use Mustache_Engine as Mustache;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Textarea,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\Hidden;

class TasksController extends ControllerBase
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
      $this->flags['time'] = 1200;

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $this->flags['status'],
      "title"     =>  $this->flags['title'],
      "text"      =>  $this->flags['text'],
      "redirect"  =>  $this->flags['redirect'],
      "time"      =>  $this->flags['time'] ,
      "target"    =>  $this->flags['target'] ,
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
  }

  public function NewAction()
  {
    $this->response->setContentType("application/json");

    $this->flags['target'] = "#createBox";

    if(!$this->request->isPost()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Cadastrar!";
      $this->flags['text']   = "Metodo Inválido.";
    endif;

    if(!$this->security->checkToken()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Cadastrar!";
      $this->flags['text']   = "Token de segurança inválido.";
    endif;

    if($this->flags['status']):

      $task = new Tasks;
        $task->title        = $this->request->getPost("title","string");
        $task->description  = $this->request->getPost("description","string");
        $task->project      = $this->request->getPost("project","int");
        $task->created      = (new \DateTime())->format("Y-m-d H:i:s");
        $task->deadline     = (new \DateTime($this->request->getPost("deadline","string")))->format("Y-m-d H:i:s");
        $task->assigned     = $this->request->getPost("assigned","int");
        $task->status       = 1;
      $task->save();

    # Log What Happend
      $this->logManager($this->logs->create,"Adicionou uma nova tarefa ( {$this->request->getPost('title','string')} )",$this->request->getPost("project","int"));

      $this->flags['title']    = "Cadastrado com Sucesso!";
      $this->flags['text']     = "Tarefa cadastrada com sucesso!";
      $this->flags['redirect'] = "/tasks";
      $this->flags['time']     = 0;

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $this->flags['status'],
      "title"     =>  $this->flags['title'],
      "text"      =>  $this->flags['text'],
      "redirect"  =>  $this->flags['redirect'],
      "time"      =>  $this->flags['time'] ,
      "target"    =>  $this->flags['target'] ,
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

  }

  public function UpdateAction()
  {
    $this->response->setContentType("application/json");

    $this->flags['target'] = "#updateBox";

    if(!$this->request->isPost()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Alterar!";
      $this->flags['text']   = "Metodo Inválido.";
    endif;

    if(!$this->security->checkToken()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Alterar!";
      $this->flags['text']   = "Token de segurança inválido.";
    endif;

    if($this->flags['status']):

      $task = Tasks::findFirst($this->dispatcher->getParam("task"));
        $task->title        = $this->request->getPost("title","string");
        $task->description  = $this->request->getPost("description","string");
        $task->project      = $this->request->getPost("project","int");
        $task->deadline     = (new \DateTime($this->request->getPost("deadline","string")))->format("Y-m-d H:i:s");
        $task->assigned     = $this->request->getPost("assigned","int");
      $task->save();

    # Log What Happend
      $this->logManager($this->logs->create,"Alterou uma tarefa ( {$this->request->getPost('title','string')} )",$this->request->getPost("project","int"));

      $this->flags['title']    = "Alterado com Sucesso!";
      $this->flags['text']     = "Tarefa Alterada com sucesso!";
      $this->flags['redirect'] = "/tasks";
      $this->flags['time']     = 1200;

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $this->flags['status'],
      "title"     =>  $this->flags['title'],
      "text"      =>  $this->flags['text'],
      "redirect"  =>  $this->flags['redirect'],
      "time"      =>  $this->flags['time'] ,
      "target"    =>  $this->flags['target'] ,
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

      # CREATING ELEMENTS
      $element['project'] = new Select( "project" , Projects::find() ,[
        'using' =>  ['_','title'],
        'title' => "Projeto Associado",
        'class' => "chosen-select form-control"
      ]);

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

      $element['assigned'] = new Select( "assigned" , Team::find() ,[
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
        $action = "/tasks/new";
        $template = "create";
        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO UPDATE POPULATE WITH VALJUE TO ELEMENT
      elseif ($this->dispatcher->getParam("method") == "update"):
        $task = Tasks::findFirst($this->dispatcher->getParam("task"));
        $action = "/tasks/update/{$task->_}";
        $template = "update";
        $element['project']->setAttribute("value",$task->project);
        $element['title']->setAttribute("value",$task->title);
        $element['description']->setAttribute("value",$task->description);
        $element['deadline']->setAttribute("value",(new \DateTime($task->deadline))->format("d-m-Y"));
        $element['assigned']->setAttribute("value",$task->assigned);
        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO VIEW POPULATE WITH VALJUE TO ELEMENT
      elseif ($this->dispatcher->getParam("method") == "view"):
        $template = "view";
        $task = Tasks::findFirst($this->dispatcher->getParam("task"));
        $element['project']->setAttribute("disabled",true)->setAttribute("value",$task->project);
        $element['title']->setAttribute("disabled",true)->setAttribute("value",$task->title);
        $element['description']->setAttribute("disabled",true)->setAttribute("value",$task->description);
        $element['deadline']->setAttribute("disabled",true)->setAttribute("value",(new \DateTime($task->deadline))->format("d-m-Y"));
        $element['assigned']->setAttribute("disabled",true)->setAttribute("value",$task->assigned);
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
