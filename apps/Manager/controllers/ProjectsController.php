<?php

namespace Manager\Controllers;

use Manager\Models\Tasks as Tasks,
    Manager\Models\Team as Team,
    Manager\Models\Clients as Clients,
    Manager\Models\Companies as Companies,
    Manager\Models\ProjectTypes as ProjectTypes,
    Manager\Models\Assignments as Assignments,
    Manager\Models\Projects as Projects;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Textarea,
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
        'Manager\Models\Clients.lastname',
        'Manager\Models\Companies.fantasy',
        'Manager\Models\ProjectTypes.title as type',
      ])
      ->leftJoin('Manager\Models\Companies', 'Manager\Models\Companies.client = \Manager\Models\Projects.client')
      ->innerJoin('Manager\Models\Clients', 'Manager\Models\Clients._ = \Manager\Models\Projects.client')
      ->innerJoin('Manager\Models\ProjectTypes', 'Manager\Models\ProjectTypes._ = Manager\Models\Projects.type')
      ->execute();

      $this->view->clients   = $clients;
      $this->view->controller = $this;

    }

    public function CreateAction()
    {
      $clients = Clients::query()
      ->columns([
        'Manager\Models\Clients._',
        'Manager\Models\Clients.firstname',
        'Manager\Models\Clients.lastname',
        'Manager\Models\Companies.fantasy',
      ])
      ->leftJoin('Manager\Models\Companies', 'Manager\Models\Companies.client = \Manager\Models\Clients._')
      ->execute();

      $clientOptions = [];

      foreach ($clients as $client) {
        $clientOptions[$client->_] = "{$client->firstname} {$client->lastname}".( $client->fantasy ? " ( {$client->fantasy} ) " :'' );
      }

      $form = new Form();

        $form->add(new Hidden( "security" ,[
            'name'  => $this->security->getTokenKey(),
            'value' => $this->security->getToken(),
        ]));

        $form->add(new Text( "title" ,[
            'class'         => "form-control",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
        ]));

        $form->add(new Text( "deadline" ,[
            'class'         => "form-control",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
        ]));

        $form->add(new Textarea( "description" ,[
            'class'         => "wysihtml form-control",
            'placeholder'   => "Breve Descrição ...",
            'style'         => "height: 250px"
        ]));

        $form->add(new Select( "client" , $clientOptions , [
          'class' => "chosen-select"
        ]));

        $form->add(new Select( "members" , Team::find() ,
        [
            'using' =>  ['uid','name'],
            'multiple'         => true ,
            'data-placeholder' => "Membros Participantes",
            'class'            => "chosen-select",
        ]));

        $form->add(new Select( "type" , ProjectTypes::find() ,
        [
            'using' =>  ['_','title'],
            'data-placeholder' => "Categoria do Projeto",
            'class'            => "chosen-select"
        ]));

      $this->view->form = $form;
    }

    public function ModifyAction()
    {
      $urlrequest = $this->dispatcher->getParam("urlrequest");
      $project = Projects::query()
      ->columns([
        'Manager\Models\Projects._',
        'Manager\Models\Projects.title',
        'Manager\Models\Projects.description',
        'Manager\Models\Projects.created',
        'Manager\Models\Projects.deadline',
        'Manager\Models\Projects.finished',
        'Manager\Models\Projects.status',
        'Manager\Models\Clients.firstname',
        'Manager\Models\Clients.lastname',
        'Manager\Models\Companies.fantasy',
        'Manager\Models\ProjectTypes.title as type',
      ])
      ->leftJoin('Manager\Models\Companies', 'Manager\Models\Companies.client = \Manager\Models\Projects.client')
      ->innerJoin('Manager\Models\Clients', 'Manager\Models\Clients._ = \Manager\Models\Projects.client')
      ->innerJoin('Manager\Models\ProjectTypes', 'Manager\Models\ProjectTypes._ = Manager\Models\Projects.type')
      ->where("Manager\Models\Projects._ = '{$urlrequest}'")
      ->limit(1)
      ->execute();

      $this->view->project = $project[0];
    }

    public function NewAction()
    {
      $this->response->setContentType("application/json");
      $flags = [
        'status'  => true,
        'title'   => false,
        'text'    => false
      ];

      if(!$this->request->isPost()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Token de segurança inválido.";
      endif;

      if($flags['status']):
        $this->response->setStatusCode(200,"OK");

        $project = new Projects;
          $project->title   = $this->request->getPost("title","string");
          $project->type    = $this->request->getPost("type","int");
          $project->client  = $this->request->getPost("client","int");
          $project->created = (new \DateTime())->format("Y-m-d H:i:s");
          $project->deadline = (new \DateTime($this->request->getPost("deadline")))->format("Y-m-d H:i:s");
          $project->status  = 1;
          $project->description  = $this->request->getPost("description");
        if($project->save())
        {
          # Assign team
          foreach(explode(',' , $this->request->getPost("members")) as $member)
          {
            $assign = new Assignments;
              $assign->project = $project->_;
              $assign->member  = $member;
            $assign->save();
          }
        }

        # Log What Happend
        $name = $this->request->getPost("title","string");
        $this->logManager($this->logs->create,"Cadastrou um novo Projeto ( {$name} ).");

        $flags['status'] = true ;
        $flags['title']  = "Cadastrado com Sucesso!";
        $flags['text']   = "Projeto Cadastrado com sucesso!";

      endif;

      return $this->response->setJsonContent([
        "status" => $flags['status'] ,
        "title"  => $flags['title'] ,
        "text"   => $flags['text']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
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

    public function ChartAction()
    {
      $this->response->setContentType("application/json");
      $flags = [
        'status'  => true,
        'title'   => false,
        'text'    => false ,
        'done'    => "Tarefas Concluídas",
        'open'    => "Tarefas em Aberto",
        'doneVal' => 0,
        'openVal' => 0,
      ];

      if(!$this->request->isGet()):
        $flags['status'] = false ;
        $flags['title']  = "Erro!";
        $flags['text']   = "Metodo Inválido.";
      endif;

      if($flags['status']):
        $this->response->setStatusCode(200,"OK");

        $tasks = $this->TaskPercentage($this->dispatcher->getParam("urlrequest"));

        $flags['status'] = true ;
        $flags['doneVal']   = $tasks;
        $flags['openVal']   = (100 - $tasks);

      endif;

      return $this->response->setJsonContent([
        "status"  => $flags['status'] ,
        "title"   => $flags['title'] ,
        "text"    => $flags['text'] ,
        "done"    => $flags['done'] ,
        "open"    => $flags['open'] ,
        "doneVal" => $flags['doneVal'] ,
        "openVal" => $flags['openVal']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}
