<?php

namespace Manager\Controllers;

use Manager\Models\Logs         as Logs,
    Manager\Models\Team         as Team,
    Manager\Models\Tasks        as Tasks,
    Manager\Models\Clients      as Clients,
    Manager\Models\Companies    as Companies,
    Manager\Models\Assignments  as Assignments,
    Manager\Models\ProjectTypes as ProjectTypes,
    Manager\Models\Projects     as Projects;

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

      $projects = Projects::query()
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

      $form = new Form();

        $form->add(new Hidden( "security" ,[
            'name'  => $this->security->getTokenKey(),
            'value' => $this->security->getToken(),
        ]));

      $this->view->form = $form;
      $this->view->projects   = $projects;
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
            'class'         => "form-control inputmask",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'data-inputmask'=> "'alias': 'dd-mm-yyyy'"
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

      $this->assets->addCss("assets/manager/css/app/timeline.css");

      $urlrequest = $this->dispatcher->getParam("project");

      # Project Query
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
      ->where("Manager\Models\Projects._ = :project:")
      ->bind([
        "project" =>  $urlrequest
      ])
      ->execute();

      # Assigned Members Query
      $members = Assignments::query()
      ->columns([
        'Manager\Models\Team._',
        'Manager\Models\Team.name',
        'Manager\Models\Team.image',
        'Manager\Models\Departments.department',
      ])
      ->innerJoin('Manager\Models\Team', 'Manager\Models\Team._ = Manager\Models\Assignments.member')
      ->innerJoin('Manager\Models\Departments', 'Manager\Models\Departments._ = Manager\Models\Team.department_id')
      ->where("Manager\Models\Assignments.project = :project:")
      ->bind([
        "project" =>  $urlrequest
      ])
      ->execute();

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
        'Manager\Models\Team.name',
      ])
      ->innerJoin('Manager\Models\Team', 'Manager\Models\Team._ = Manager\Models\Tasks.assigned')
      ->where("Manager\Models\Tasks.project = :project:")
      ->bind([
        "project" =>  $urlrequest
      ])
      ->orderBy("status ASC , created DESC")
      ->execute();

      $projectMembers = Assignments::query()
      ->columns([
        'Manager\Models\Team._',
        'Manager\Models\Team.name'
      ])
      ->innerJoin('Manager\Models\Team', 'Manager\Models\Assignments.member = Manager\Models\Team._')
      ->where("Manager\Models\Assignments.project = :project:")
      ->bind([
        "project" =>  $urlrequest
      ])
      ->execute();

      #  Query method not working in Select , so heres a work around
      foreach($projectMembers as $pm)
      {
        $availableMembers[$pm->_] = $pm->name;
      }

      $assign = Assignments::findByProject($urlrequest);
      $clause = [];
      foreach($assign as $i => $a)
      {
        if ($i === (count($assign) - 1)):
          array_push($clause, "_ != '{$a->member}'");
        else:
          array_push($clause, "_ != '{$a->member}' AND ");
        endif;
      }

      $form = new Form();

        $form->add(new Hidden( "security" ,[
          'name'  => $this->security->getTokenKey(),
          'value' => $this->security->getToken(),
        ]));

        $form->add(new Select( "members" , Team::find([ implode(" ",$clause) ]) ,
        [
          'using' =>  ['uid','name'],
          'data-placeholder' => "Membros",
          'class'            => "form-control",
        ]));

        $form->add(new Text( "new_title",[
          'class'         => "form-control",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
        ]));

        $form->add(new Textarea( "new_description",[
          'class'         => "form-control",
        ]));

        $form->add(new Select( "new_members" , $availableMembers ,
        [
          'using' =>  ['_','name'],
          'data-placeholder' => "Membros",
          'class'            => "form-control",
        ]));

        $form->add(new Text( "new_deadline",[
          'class'         => "form-control inputmask",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
          'data-inputmask'=> "'alias': 'dd-mm-yyyy'"
        ]));

      $this->view->project = $project[0];
      $this->view->members = $members;
      $this->view->tasks = $tasks;
      $this->view->logs = Logs::findByProject($urlrequest);
      $this->view->form = $form;
      $this->view->controller = $this;
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

    public function RemoveAction()
    {
      $this->response->setContentType("application/json");
      $flags = [
        'status'    => true,
        'title'     => false,
        'text'      => false,
        'redirect'  => false,
        'time'      => 0,
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

        $p = Projects::findFirst($this->dispatcher->getParam("project"));

          foreach(Tasks::findByProject($p->_) as $t)
          {
            $t->delete();
          }
          foreach(Assignments::findByProject($p->_) as $a)
          {
            $a->delete();
          }

        $p->delete();

        # Log What Happend
        $this->logManager($this->logs->delete,"Removeu um projeto ( {$p->title} ).");

        $flags['title']     = "Removido Com Sucesso!";
        $flags['text']      = "Projeto Removido com Sucesso.";
        $flags['redirect']  = "/projects";
        $flags['time']      = 1000;

      endif;

      return $this->response->setJsonContent([
        "status"    =>  $flags['status'],
        "title"     =>  $flags['title'],
        "text"      =>  $flags['text'],
        "redirect"  =>  $flags['redirect'],
        "time"      =>  $flags['time']
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
        'done'    => "Concluídos",
        'open'    => "Em Aberto",
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

        $tasks = $this->TaskPercentage($this->dispatcher->getParam("project"));

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

    public function NewMemberAction()
    {
      $this->response->setContentType("application/json");
      $flags = [
        'status'  => true,
        'title'   => false,
        'text'    => false ,
        'redirect' => false,
        'time'    => 0
      ];

      if(!$this->request->isPost()):
        $flags['status'] = false ;
        $flags['title']  = "Erro!";
        $flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Token de segurança inválido.";
      endif;

      if($flags['status']):
        $this->response->setStatusCode(200,"OK");

        $project = $this->dispatcher->getParam("project");

        $assign = new Assignments;
          $assign->project = $project;
          $assign->member = $this->request->getPost("members");
        $assign->save();

        $member = Team::findFirst($this->request->getPost("members"))->name;
        $name = Projects::findFirst($project)->title;

        # Log What Happend
        $this->logManager($this->logs->update,"Adicionou {$member} ao projeto {$name}.");

        $flags['status'] = true ;
        $flags['title']  = "Adicionado Com Sucesso!";
        $flags['text']   = "Membro adicionado com sucesso ao projeto!";
        $flags['redirect']   = "/projects/modify/{$project}";
        $flags['time']   = 1200;

      endif;

      return $this->response->setJsonContent([
        "status"  => $flags['status'] ,
        "title"   => $flags['title'] ,
        "text"    => $flags['text'] ,
        "redirect"    => $flags['redirect'] ,
        "time"    => $flags['time']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function RemoveMemberAction()
    {
      $this->response->setContentType("application/json");
      $flags = [
        'status'  => true,
        'title'   => false,
        'text'    => false ,
        'redirect'  => false,
        'time'      => 0,
      ];

      if(!$this->request->isPost()):
        $flags['status'] = false ;
        $flags['title']  = "Erro!";
        $flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Token de segurança inválido.";
      endif;

      if($flags['status']):
        $this->response->setStatusCode(200,"OK");

        $project = $this->dispatcher->getParam("project");
        $member = $this->dispatcher->getParam("member");

        foreach(Assignments::find(["project = '{$project}' AND member = '{$member}'"]) as $assign)
        {
          $assign->delete();
        }

        $member = Team::findFirst($member)->name;
        $name = Projects::findFirst($project)->title;

        # Log What Happend
        $this->logManager($this->logs->delete,"Removeu {$member} do projeto {$name}.");

        $flags['status'] = true ;
        $flags['title']  = "Removido Com Sucesso!";
        $flags['text']   = "Membro removido com sucesso do projeto!";
        $flags['redirect']   = "/projects/modify/{$project}";
        $flags['time']   = 1800;

      endif;

      return $this->response->setJsonContent([
        "status"  => $flags['status'] ,
        "title"   => $flags['title'] ,
        "text"    => $flags['text'],
        "redirect"    => $flags['redirect'] ,
        "time"    => $flags['time']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function NewTaskAction()
    {
      $this->response->setContentType("application/json");
      $flags = [
        'status'  => true,
        'title'   => false,
        'text'    => false ,
        'redirect' => false,
        'time'    => 0
      ];

      if(!$this->request->isPost()):
        $flags['status'] = false ;
        $flags['title']  = "Erro!";
        $flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Token de segurança inválido.";
      endif;

      if($flags['status']):
        $this->response->setStatusCode(200,"OK");

        $project = $this->dispatcher->getParam("project");

        $task = new Tasks;
          $task->project      = $project;
          $task->title        = $this->request->getPost("new_title");
          $task->description  = $this->request->getPost("new_description");
          $task->deadline     = (new \DateTime($this->request->getPost("new_deadline")))->format("Y-m-d H:i:s");
          $task->created      = (new \DateTime())->format("Y-m-d H:i:s");
          $task->assigned     = $this->request->getPost("new_members");
          $task->status       = 1;
        $task->save();

        $name = Projects::findFirst($project)->title;
        # Log What Happend
        $this->logManager($this->logs->create,"Cadastrou uma nova tarefa ao projeto {$name}.");

        $flags['status'] = true ;
        $flags['title']  = "Adicionado Com Sucesso!";
        $flags['text']   = "Tarefa adicionada com sucesso ao projeto!";
        $flags['redirect']   = "/projects/modify/{$project}";
        $flags['time']   = 1200;

      endif;

      return $this->response->setJsonContent([
        "status"  => $flags['status'] ,
        "title"   => $flags['title'] ,
        "text"    => $flags['text'] ,
        "redirect"    => $flags['redirect'] ,
        "time"    => $flags['time']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function RemoveTaskAction()
    {
      $this->response->setContentType("application/json");
      $flags = [
        'status'  => true,
        'title'   => false,
        'text'    => false ,
        'redirect'  => false,
        'time'      => 0,
      ];

      if(!$this->request->isPost()):
        $flags['status'] = false ;
        $flags['title']  = "Erro!";
        $flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Token de segurança inválido.";
      endif;

      if($flags['status']):
        $this->response->setStatusCode(200,"OK");

        $project = $this->dispatcher->getParam("project");

        $task = Tasks::findFirst($this->dispatcher->getParam("task"));
        $task->delete();

        $name = Projects::findFirst($project)->title;
        # Log What Happend
        $this->logManager($this->logs->delete,"Removeu uma nova tarefa do projeto {$name}.");

        $flags['status'] = true ;
        $flags['title']  = "Removido Com Sucesso!";
        $flags['text']   = "Tarefa removida com sucesso do projeto!";
        $flags['redirect']   = "/projects/modify/{$project}";
        $flags['time']   = 1800;

      endif;

      return $this->response->setJsonContent([
        "status"  => $flags['status'] ,
        "title"   => $flags['title'] ,
        "text"    => $flags['text'],
        "redirect"    => $flags['redirect'] ,
        "time"    => $flags['time']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}
