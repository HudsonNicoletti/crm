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
    private $flags = [
      'status'    => true,
      'title'     => false,
      'text'      => false,
      'redirect'  => false,
      'time'      => false,
      'target'    => false
    ];

    private function VerifyTasks()
    {
      $tasks = Tasks::find(["group"=>"project"]);
      foreach($tasks as $task)
      {
        $project = Projects::findFirst($task->project);

        if( ($this->TaskPercentage($task->project) - 100) == 0 )
        {
          $project->status = 2;
        }
        else
        {
          $project->status = 1;
        }
        $project->save();
      }
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

      if(!$this->request->isGet()):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro!";
        $this->flags['text']   = "Metodo Inválido.";
      endif;

      if($this->flags['status']):
        $this->response->setStatusCode(200,"OK");

        $tasks = $this->TaskPercentage($this->dispatcher->getParam("project"));

        $this->flags['status'] = true ;
        $this->flags['doneVal']   = $tasks;
        $this->flags['openVal']   = (100 - $tasks);

      endif;

      return $this->response->setJsonContent([
        "status"  => $this->flags['status'] ,
        "title"   => $this->flags['title'] ,
        "text"    => $this->flags['text'] ,
        "done"    => "Concluídos" ,
        "open"    => "Em Aberto" ,
        "doneVal" => $this->flags['doneVal'] ,
        "openVal" => $this->flags['openVal']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function IndexAction()
    {
      $this->assets
      ->addCss("assets/manager/css/app/email.css")
      ->addCss('assets/manager/css/plugins/bootstrap-chosen/chosen.css')
      ->addJs("assets/manager/js/plugins/jquery.filtr.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js")
      ->addJs('assets/manager/js/plugins/inputmask/jquery.inputmask.bundle.js')
      ->addJs("assets/manager/js/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.js")
      ->addJs("assets/manager/js/plugins/bootstrap-chosen/chosen.jquery.js");

      # Verify All Tasks First
      $this->VerifyTasks();

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
      ]));

      $form->add(new Select( "client" , $clientOptions , [
        'class' => "chosen-select"
      ]));

      $form->add(new Select( "members" , Team::find() ,
      [
        'using' =>  ['uid','name'],
        'data-placeholder' => "Membros Participantes",
        'multiple'         => true,
        'class'            => "chosen-select form-control",
      ]));

      $form->add(new Select( "type" , ProjectTypes::find() ,
      [
        'using' =>  ['_','title'],
        'data-placeholder' => "Categoria do Projeto",
        'class'            => "chosen-select"
      ]));

      $this->view->form       = $form;
      $this->view->projects   = $projects;
      $this->view->controller = $this;

    }

    public function CategoriesAction()
    {
      $this->assets
      ->addCss("assets/manager/css/app/email.css")
      ->addCss('assets/manager/css/plugins/bootstrap-chosen/chosen.css')
      ->addJs("assets/manager/js/plugins/jquery.filtr.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js")
      ->addJs("assets/manager/js/plugins/bootstrap-chosen/chosen.jquery.js");

      $categories = ProjectTypes::find(['order'=>"title ASC"]);

      $form = new Form();
      $form->add(new Hidden( "security" ,[
        'name'  => $this->security->getTokenKey(),
        'value' => $this->security->getToken(),
      ]));

      $form->add(new Text( "category" ,[
        'class'         => "form-control",
        'data-validate' => true,
        'data-empty'    => "* Campo Obrigatório",
      ]));

      $form->add(new Select( "categories" , ProjectTypes::find() ,
      [
        'using' =>  ['_','title'],
        'data-placeholder' => "Categoria do Projeto",
        'class'            => "chosen-select"
      ]));

      $this->view->form = $form;
      $this->view->categories = $categories;
    }

    public function OverviewAction()
    {
      $this->assets
      ->addCss("assets/manager/css/app/email.css")
      ->addCss("assets/manager/css/app/timeline.css")
      ->addCss('assets/manager/css/plugins/bootstrap-chosen/chosen.css')
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js")
      ->addJs("assets/manager/js/plugins/DevExpressChartJS/dx.chartjs.js")
      ->addJs("assets/manager/js/plugins/bootstrap-chosen/chosen.jquery.js");

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
        'Manager\Models\Projects.client',
        'Manager\Models\Projects.type as filter',
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
        'Manager\Models\Team.uid',
        'Manager\Models\Team.name',
        'Manager\Models\Team.image',
        'Manager\Models\Departments.department',
      ])
      ->innerJoin('Manager\Models\Team', 'Manager\Models\Team.uid = Manager\Models\Assignments.member')
      ->innerJoin('Manager\Models\Departments', 'Manager\Models\Departments._ = Manager\Models\Team.department_id')
      ->where("Manager\Models\Assignments.project = :project:")
      ->bind([
        "project" =>  $urlrequest
      ])
      ->execute();

      $assign = Assignments::findByProject($urlrequest);
      $clause = [];
      foreach($assign as $i => $a)
      {
        if ($i === (count($assign) - 1)):
          array_push($clause, "uid != '{$a->member}'");
        else:
          array_push($clause, "uid != '{$a->member}' AND ");
        endif;
      }

      $form = new Form();

      $form->add(new Hidden( "security" ,[
        'name'  => $this->security->getTokenKey(),
        'value' => $this->security->getToken(),
        'id'    => false
      ]));

      $form->add(new Select( "members" , Team::find([ implode(" ",$clause) ]) ,
      [
        'using' =>  ['uid','name'],
        'id'               => false,
        'data-placeholder' => "Membros",
        'class'            => "chosen-select",
      ]));

      $this->view->project = $project[0];
      $this->view->members = $members;
      $this->view->logs = Logs::find([ "project = '{$urlrequest}'", "order" => "_ DESC" ]);
      $this->view->form = $form;
      $this->view->controller = $this;
    }

    public function SettingsAction()
    {
      $this->assets
      ->addCss("assets/manager/css/app/email.css")
      ->addCss('assets/manager/css/plugins/bootstrap-chosen/chosen.css')
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js")
      ->addJs('assets/manager/js/plugins/inputmask/jquery.inputmask.bundle.js')
      ->addJs("assets/manager/js/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.js")
      ->addJs("assets/manager/js/plugins/bootstrap-chosen/chosen.jquery.js");

      $form = new Form();

      $project = Projects::findFirst($this->dispatcher->getParam("project"));
      $clients = Clients::query()
      ->columns([
        'Manager\Models\Clients._',
        'Manager\Models\Clients.firstname',
        'Manager\Models\Clients.lastname',
        'Manager\Models\Companies.fantasy',
      ])
      ->leftJoin('Manager\Models\Companies', 'Manager\Models\Companies.client = \Manager\Models\Clients._')
      ->execute();

      foreach ($clients as $client) {
        $clientOptions[$client->_] = "{$client->firstname} {$client->lastname}".( $client->fantasy ? " ( {$client->fantasy} ) " :'' );
      }

      $form->add(new Hidden( "security" ,[
        'name'  => $this->security->getTokenKey(),
        'value' => $this->security->getToken(),
        'id'    => false
      ]));

      $form->add(new Text( "project_title" ,[
        'class'         => "form-control",
        'data-validate' => true,
        'data-empty'    => "* Campo Obrigatório",
        'value'         => $project->title
      ]));

      $form->add(new Text( "project_deadline" ,[
        'class'         => "form-control inputmask",
        'data-validate' => true,
        'data-empty'    => "* Campo Obrigatório",
        'data-inputmask'=> "'alias': 'dd-mm-yyyy'",
        'value'         => (new \DateTime($project->deadline))->format("d-m-Y")
      ]));

      $form->add(new Textarea( "project_description" ,[
        'class'         => "wysihtml form-control",
        'placeholder'   => "Breve Descrição ...",
        'style'         => "height: 250px",
        'value'         => $project->description
      ]));

      $form->add(new Select( "project_client" , $clientOptions , [
        'class' => "chosen-select",
        'value' => $project->client,
      ]));

      $form->add(new Select( "project_type" , ProjectTypes::find() ,
      [
        'using' =>  ['_','title'],
        'data-placeholder' => "Categoria do Projeto",
        'class'            => "chosen-select form-control",
        'value'            => $project->type
      ]));

      $this->view->form = $form;
      $this->view->project = $project;

    }

    public function NewAction()
    {
      $this->response->setContentType("application/json");

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

        $this->flags['status'] = true ;
        $this->flags['title']  = "Cadastrado com Sucesso!";
        $this->flags['text']   = "Projeto Cadastrado com sucesso!";
        $this->flags['redirect']   = "/projects";
        $this->flags['time']   = 1200;

      endif;

      return $this->response->setJsonContent([
        "status"    => $this->flags['status'] ,
        "title"     => $this->flags['title'] ,
        "text"      => $this->flags['text'],
        "redirect"  => $this->flags['redirect'],
        "time"      => $this->flags['time'],
        "target"    => $this->flags['target'],
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function UpdateAction()
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

        $id    = $this->dispatcher->getParam("project");
        $title = $this->request->getPost("project_title","string");

        $project = Projects::findFirst($id);
          $project->title   = $title;
          $project->type    = $this->request->getPost("project_type","int");
          $project->client  = $this->request->getPost("project_client","int");
          $project->deadline  = (new \DateTime($this->request->getPost("project_deadline","string")))->format("Y-m-d H:i:s");
          $project->description  = $this->request->getPost("project_description");
        $project->save();
        # Log What Happend
        $this->logManager($this->logs->update,"Alterou os dados do projeto {$name}.",$id);

        $this->flags['status'] = true ;
        $this->flags['title']  = "Alterado Com Sucesso!";
        $this->flags['text']   = "Projeto alterado com sucesso!";
        $this->flags['redirect']   = "/project/overview/{$id}";
        $this->flags['time']   = 1200;

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
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Token de segurança inválido.";
      endif;

      if($this->flags['status']):

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

        $this->flags['title']     = "Removido Com Sucesso!";
        $this->flags['text']      = "Projeto Removido com Sucesso.";
        $this->flags['redirect']  = "/projects";
        $this->flags['time']      = 1000;

      endif;

      return $this->response->setJsonContent([
        "status"    =>  $this->flags['status'],
        "title"     =>  $this->flags['title'],
        "text"      =>  $this->flags['text'],
        "redirect"  =>  $this->flags['redirect'],
        "time"      =>  $this->flags['time'],
        "target"    =>  $this->flags['target'],
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function NewCategoryAction()
    {
      $this->response->setContentType("application/json");
      # Target Response Box
      $this->flags['target']    = "#alertBox";

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

        $title = $this->request->getPost("category","string");

        $category = new ProjectTypes;
          $category->title = $title;
        $category->save();

        # Log What Happend
        $this->logManager($this->logs->create,"Cadastrou uma categoria de projetos ( {$title} ).");

        $this->flags['status']    = true ;
        $this->flags['title']     = "Cadastrado com Sucesso!";
        $this->flags['text']      = "Categoria Cadastrada com sucesso!";
        $this->flags['redirect']  = "/projects/categories";
        $this->flags['time']      = 1200;

      endif;

      return $this->response->setJsonContent([
        "status"    => $this->flags['status'] ,
        "title"     => $this->flags['title'] ,
        "text"      => $this->flags['text'],
        "redirect"  => $this->flags['redirect'],
        "time"      => $this->flags['time'],
        "target"    => $this->flags['target']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function UpdateCategoryAction()
    {
      $this->response->setContentType("application/json");
      # Target Response Box
      $this->flags['target']    = "#editBox";

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

        $title = $this->request->getPost("category","string");

        $category = ProjectTypes::findFirst($this->dispatcher->getParam("type"));
          $category->title = $title;
        $category->save();

        # Log What Happend
        $this->logManager($this->logs->update,"Alterou uma categoria de projetos ( {$title} ).");

        $this->flags['status']    = true ;
        $this->flags['title']     = "Alterado com Sucesso!";
        $this->flags['text']      = "Categoria ALterada com sucesso!";
        $this->flags['redirect']  = "/projects/categories";
        $this->flags['time']      = 1200;

      endif;

      return $this->response->setJsonContent([
        "status"    => $this->flags['status'] ,
        "title"     => $this->flags['title'] ,
        "text"      => $this->flags['text'],
        "redirect"  => $this->flags['redirect'],
        "time"      => $this->flags['time'],
        "target"    => $this->flags['target']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function RemoveCategoryAction()
    {
      $this->response->setContentType("application/json");
      # Target Response Box
      $this->flags['target']    = "#removeBox";

      if(!$this->request->isPost()):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Remover!";
        $this->flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Remover!";
        $this->flags['text']   = "Token de segurança inválido.";
      endif;

      if($this->flags['status']):

        $category = ProjectTypes::findFirst($this->dispatcher->getParam("type"));
          $title = $category->title;
        $category->delete();

        foreach(Projects::findByType($this->dispatcher->getParam("type")) as $p)
        {
          $p->type = $this->request->getPost("categories");
          $p->save();
        }

        # Log What Happend
        $this->logManager($this->logs->delete,"Removeu uma categoria de projetos ( {$title} ).");

        $this->flags['status']    = true ;
        $this->flags['title']     = "Removido com Sucesso!";
        $this->flags['text']      = "Categoria Removida com sucesso!";
        $this->flags['redirect']  = "/projects/categories";
        $this->flags['time']      = 1200;

      endif;

      return $this->response->setJsonContent([
        "status"    => $this->flags['status'] ,
        "title"     => $this->flags['title'] ,
        "text"      => $this->flags['text'],
        "redirect"  => $this->flags['redirect'],
        "time"      => $this->flags['time'],
        "target"    => $this->flags['target']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}
