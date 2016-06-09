<?php

namespace Manager\Controllers;

use \Manager\Models\Team as Team,
    \Manager\Models\Users as Users,
    \Manager\Models\Tasks        as Tasks,
    \Manager\Models\Assignments  as Assignments,
    \Manager\Models\Departments as Departments;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\File,
    Phalcon\Forms\Element\Hidden;

use \Phalcon\Mvc\Model\Query\Builder as Builder;

class TeamController extends ControllerBase
{
  private  $flags = [
    'status'    => true,
    'title'     => false,
    'text'      => false,
    'redirect'  => false,
    'time'      => null
  ];

  public function IndexAction()
  {
    $this->assets
    ->addCss("assets/manager/css/app/email.css")
    ->addJs("assets/manager/js/plugins/jquery.filtr.min.js")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

    $form = new Form();

    $form->add(new Hidden( "security" ,[
      'name'  => $this->security->getTokenKey(),
      'value' => $this->security->getToken(),
    ]));

    $form->add(new Select("select", Team::find(),[
      'using' => ['uid','name'],
      'class' => "form-control"
    ]));

    $team = Users::query()
    ->columns([
      'Manager\Models\Users._',
      'Manager\Models\Users.email',
      'Manager\Models\Team.name',
      'Manager\Models\Team.uid',
      'Manager\Models\Team.image',
      'Manager\Models\Departments.department',
      'Manager\Models\Team.phone'
    ])
    ->innerJoin('Manager\Models\Team', 'Manager\Models\Team.uid = Manager\Models\Users._')
    ->innerJoin('Manager\Models\Departments', 'Manager\Models\Team.department_id = Manager\Models\Departments._')
    ->execute();

    $this->view->form = $form;
    $this->view->members = $team;
  }

  public function CreateAction()
  {
    $this->assets
    ->addCss("assets/manager/css/app/email.css")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

    $form = new Form();
    $form->add(new Hidden( "security" ,[
      'name'  => $this->security->getTokenKey(),
      'value' => $this->security->getToken()
    ]));

    $form->add(new Text( "name" ,[
      'class'         => "form-control",
      'id'            => "name",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
    ]));

    $form->add(new Text( "email" ,[
      'class'         => "form-control",
      'id'            => "email",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
    ]));

    $form->add(new Text( "phone" ,[
      'class'         => "form-control",
      'id'            => "phone",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
    ]));

    $form->add(new Text( "username" ,[
      'class'         => "form-control",
      'id'            => "username",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
    ]));

    $form->add(new Password( "password" ,[
      'class'         => "form-control",
      'id'            => "password",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
    ]));

    $form->add(new Select("department", Departments::find(),[
      'using' => ['_','department'],
      'class' => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
    ]));

    $form->add(new File("image",[
      'class' => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
    ]));

    $this->view->form = $form;
  }

  public function ModifyAction()
  {
    $this->assets
    ->addCss("assets/manager/css/app/email.css")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

    $user   = Users::findFirst($this->dispatcher->getParam("urlrequest"));
    $member = Team::findFirstByUid($user->_);

    $form = new Form();

    $form->add(new Hidden( "security" ,[
      'name'  => $this->security->getTokenKey(),
      'value' => $this->security->getToken()
    ]));

    $form->add(new Text( "name" ,[
      'class'         => "form-control",
      'id'            => "name",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $member->name
    ]));

    $form->add(new Text( "email" ,[
      'class'         => "form-control",
      'id'            => "email",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $user->email
    ]));

    $form->add(new Text( "phone" ,[
      'class'         => "form-control",
      'id'            => "phone",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $member->phone
    ]));

    $form->add(new Text( "username" ,[
      'class'         => "form-control",
      'id'            => "username",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $user->username
    ]));

    $form->add(new Password( "password" ,[
      'class'         => "form-control",
      'id'            => "password",
    ]));

    $form->add(new Select("department", Departments::find(),[
      'using' => ['_','department'],
      'class' => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $member->department_id
    ]));

    $form->add(new File("image",[
      'class' => "form-control",
    ]));

      $this->view->form = $form;
      $this->view->uid = $this->dispatcher->getParam("urlrequest");
  }

  public function DepartmentsAction()
  {
    $this->assets
    ->addCss("assets/manager/css/app/email.css")
    ->addJs("assets/manager/js/plugins/jquery.filtr.min.js")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

    $form = new Form();

    $form->add(new Hidden( "security" ,[
      'name'  => $this->security->getTokenKey(),
      'value' => $this->security->getToken(),
    ]));

    $form->add(new text( "department" ,[
      'class' => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
    ]));

    $form->add(new Select("departments", Departments::find(),[
      'using' => ['_','department'],
      'class' => "form-control"
    ]));

    $this->view->form = $form;
    $this->view->departments = Departments::find();
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

    if(!$this->request->hasFiles()):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Cadastrar!";
      $this->flags['text']   = "Nehuma Imagem Selecionada!";
    endif;

    if(!$this->isEmail($this->request->getPost("email"))):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Cadastrar!";
      $this->flags['text']   = "Endereço de E-Mail inválido!";
    endif;

    if( Users::findFirstByEmail($this->request->getPost("email"))->_ != NULL ):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Cadastrar!";
      $this->flags['text']   = "Endereço de E-Mail já cadastrado!";
    endif;

    if( Users::findFirstByUsername($this->request->getPost("username"))->_ != NULL ):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Cadastrar!";
      $this->flags['text']   = "Nome de usuário já cadastrado!";
    endif;

    if($this->flags['status']):

      foreach($this->request->getUploadedFiles() as $file)
      {
        $filename = substr(sha1(uniqid()), 0, 12).'.'.$file->getExtension();
        $file->moveTo("assets/manager/images/avtar/{$filename}");
      }

      $user = new Users;
        $user->username   = $this->request->getPost("username");
        $user->password   = password_hash($this->request->getPost("password"), PASSWORD_BCRYPT );
        $user->email      = $this->request->getPost("email");
        $user->permission = $this->permissions->team;
      if($user->save())
      {
        $member = new Team;
          $member->uid = $user->_;
          $member->name = $this->request->getPost("name");
          $member->phone = $this->request->getPost("phone");
          $member->image = $filename;
          $member->department_id = $this->request->getPost("department");
        $member->save();
      }

      $name = $this->request->getPost("name");
      # Log What Happend
      $this->logManager($this->logs->create,"Cadastrou um novo membro de equipe ({$name}).");

      $this->flags['title']  = "Cadastrado com Sucesso!";
      $this->flags['text']   = "Membro cadastrado com sucesso!";
      $this->flags['redirect']   = "/team";
      $this->flags['time']   = 1200;

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $this->flags['status'],
      "title"     =>  $this->flags['title'],
      "text"      =>  $this->flags['text'],
      "redirect"  =>  $this->flags['redirect'],
      "time"      =>  $this->flags['time'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

  }

  public function NewDepartmentAction()
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

      $d = new Departments();
        $d->department = $this->request->getPost("department","string");
      $d->save();

      $name = $this->request->getPost("department","string");
      # Log What Happend
      $this->logManager($this->logs->create,"Cadastrou um novo departamento ({$name}).");

      $this->flags['title']  = "Cadastrado com Sucesso!";
      $this->flags['text']   = "Departamento cadastrado com sucesso! A página irá atualizar.";
      $this->flags['redirect']   = "/team/departments";
      $this->flags['time']   = 2200;

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

  public function UpdateAction()
  {
    $this->response->setContentType("application/json");

    $u = Users::findFirstBy_($this->dispatcher->getParam("urlrequest"));
    $m = Team::findFirstByUid($u->_);

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

    if(!$this->isEmail($this->request->getPost("email"))):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Alterar!";
      $this->flags['text']   = "Endereço de E-Mail inválido!";
    endif;

    if( $this->request->getPost("email") != $u->email && Users::findFirstByEmail($this->request->getPost("email"))->_ != NULL ):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Alterar!";
      $this->flags['text']   = "Endereço de E-Mail já cadastrado!";
    endif;

    if( $this->request->getPost("username") != $u->username && Users::findFirstByUsername($this->request->getPost("username"))->_ != NULL ):
      $this->flags['status'] = false ;
      $this->flags['title']  = "Erro ao Alterar!";
      $this->flags['text']   = "Nome de usuário já cadastrado!";
    endif;

    if($this->flags['status']):

      if($this->request->hasFiles()):
        unlink("assets/manager/images/avtar/{$m->image}");
        foreach($this->request->getUploadedFiles() as $file):
          $filename = substr(sha1(uniqid()), 0, 12).'.'.$file->getExtension();
          $file->moveTo("assets/manager/images/avtar/{$filename}");
        endforeach;
      else:
        $filename = $m->image;
      endif;

        $u->username   = $this->request->getPost("username");
        $u->password   = ($this->request->getPost("password") != null ) ? password_hash($this->request->getPost("password"), PASSWORD_BCRYPT ) : $u->password;
        $u->email      = $this->request->getPost("email");
        $u->permission = $this->permissions->team;
        $u->save();

        $m->name          = $this->request->getPost("name");
        $m->phone         = $this->request->getPost("phone");
        $m->department_id = $this->request->getPost("department");
        $m->image         = $filename;
        $m->save();

      $name = $this->request->getPost("name");
      # Log What Happend
      $this->logManager($this->logs->update,"Alterou informações de um membro ({$name}).");

      $this->flags['title']  = "Alterado com Sucesso!";
      $this->flags['text']   = "Informaçoes alteradas com sucesso!";
      $this->flags['redirect']   = "/team";
      $this->flags['time']   = 1200;

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $this->flags['status'],
      "title"     =>  $this->flags['title'],
      "text"      =>  $this->flags['text'],
      "redirect"  =>  $this->flags['redirect'],
      "time"      =>  $this->flags['time'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

  }

  public function UpdateDepartmentAction()
  {
    $this->response->setContentType("application/json");

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
      $this->response->setStatusCode(200,"OK");

      $d = Departments::findFirst($this->dispatcher->getParam("urlrequest"));
        $d->department = $this->request->getPost("department","string");
      $d->save();

      $name = $this->request->getPost("department");
      # Log What Happend
      $this->logManager($this->logs->update,"Alterou nome de um departamento para ({$name}).");

      $this->flags['title']  = "Alterado com Sucesso!";
      $this->flags['text']   = "Departamento alterado com sucesso! A página irá atualizar.";
      $this->flags['redirect']   =  '/team/departments';
      $this->flags['time']   = 2200;

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

  public function RemoveAction()
  {
    $this->response->setContentType("application/json");

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

    if($this->request->getPost('select') === $this->dispatcher->getParam('urlrequest')):
      $this->flags['status']    = false ;
      $this->flags['title']     = "Atenção!";
      $this->flags['text']      = "É necessário selecionar um outro membro para assumir responsabilidade de todos os projetos que o mesmo seja responsável.";
    endif;


    if($this->flags['status']):

      $member = Team::findFirstByUid($this->dispatcher->getParam('urlrequest'));
      $user   = Users::findFirst($this->dispatcher->getParam('urlrequest'));

      # remove image from server
      unlink("assets/manager/images/avtar/{$member->image}");

      # update projects assignments table
      foreach (Assignments::findByMember($member->_) as $assign)
      {
        $assign->delete();
      }
      foreach (Tasks::findByAssigned($member->_) as $task)
      {
        $task->assigned = $this->request->getPost("select");
        $task->save();
      }

      # Log What Happend
      $this->logManager($this->logs->delete,"Removeu um membro da equipe ({$member->name}).");

      $member->delete();
      $user->delete();

      $this->flags['title']  = "Removido Com Successo";
      $this->flags['text']   = "Membro da equipe removido ";

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $this->flags['status'],
      "title"     =>  $this->flags['title'],
      "text"      =>  $this->flags['text'],
      "redirect"  =>  $this->flags['redirect'],
      "time"      =>  $this->flags['time'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
  }

  public function RemoveDepartmentAction()
  {
    $this->response->setContentType("application/json");

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

    if($this->request->getPost('departments') === $this->dispatcher->getParam('urlrequest')):
      $this->flags['status']    = false ;
      $this->flags['title']     = "Atenção!";
      $this->flags['text']      = "É necessário selecionar um outro membro para assumir responsabilidade de todos os projetos que o mesmo seja responsável.";
    endif;


    if($this->flags['status']):

      $d = Departments::findFirst($this->dispatcher->getParam('urlrequest'));

      $m = Team::findByDepartment_id($d->_);
      foreach($m as $t)
      {
        $t->department_id = $this->request->getPost("departments");
        $t->save();
      }

      # Log What Happend
      $this->logManager($this->logs->delete,"Removeu um deparamento ({$d->department}).");

      $d->delete();

      $this->flags['title']  = "Removido Com Successo";
      $this->flags['text']   = "Departamento da equipe removido ";
      $this->flags['redirect']   = "/team/departments";
      $this->flags['time']   = 1200;

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $this->flags['status'],
      "title"     =>  $this->flags['title'],
      "text"      =>  $this->flags['text'],
      "redirect"  =>  $this->flags['redirect'],
      "time"      =>  $this->flags['time'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
  }


}
