<?php

namespace Manager\Controllers;

use \Manager\Models\Team as Team,
    \Manager\Models\Users as Users,
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

  public function IndexAction()
  {
    $form = new Form();

      $form->add(new Hidden( "security" ,[
          'name'  => $this->security->getTokenKey(),
          'value' => $this->security->getToken(),
      ]));

      $form->add(new Select("select", Team::find(),[
            'using' => ['uid','name'],
            'class' => "form-control"
      ]));

    $params = [
       'models'     => ['Manager\Models\Users'],
       'columns'    => ['Manager\Models\Users._','email','name','image','department','phone'],
    ];
    $builder = new Builder($params);
    $builder->innerJoin('Manager\Models\Team', 'Manager\Models\Team.uid = Manager\Models\Users._');
    $builder->innerJoin('Manager\Models\Departments', 'Manager\Models\Team.department_id = Manager\Models\Departments._');

    $team = $this->modelsManager->executeQuery($builder->getPhql());

    $this->view->form = $form;
    $this->view->members = $team;
  }

  public function CreateAction()
  {
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

        $form->add(new Select("permission",[
            $this->permissions->team  => "Equipe",
            $this->permissions->admin => "Administrador",
        ],
        [
            'class'         => "form-control",
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

        $form->add(new Select("permission",[
            $this->permissions->team  => "Equipe",
            $this->permissions->admin => "Administrador",
        ],
        [
            'class'         => "form-control",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $user->permission
        ]));

        $form->add(new File("image",[
            'class' => "form-control",
        ]));

      $this->view->form = $form;
      $this->view->uid = $this->dispatcher->getParam("urlrequest");
  }

  public function DepartmentsAction()
  {
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
    $flags = [
      'status'    => true,
      'title'     => false,
      'text'      => false,
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

    if(!$this->request->hasFiles()):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Cadastrar!";
      $flags['text']   = "Nehuma Imagem Selecionada!";
    endif;

    if(!$this->isEmail($this->request->getPost("email"))):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Cadastrar!";
      $flags['text']   = "Endereço de E-Mail inválido!";
    endif;

    if( Users::findFirstByEmail($this->request->getPost("email"))->_ != NULL ):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Cadastrar!";
      $flags['text']   = "Endereço de E-Mail já cadastrado!";
    endif;

    if( Users::findFirstByUsername($this->request->getPost("username"))->_ != NULL ):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Cadastrar!";
      $flags['text']   = "Nome de usuário já cadastrado!";
    endif;

    if($flags['status']):
      $this->response->setStatusCode(200,"OK");

      foreach($this->request->getUploadedFiles() as $file)
      {
          $filename = substr(sha1(uniqid()), 0, 12).'.'.$file->getExtension();
          $file->moveTo("assets/manager/images/avtar/{$filename}");
      }

      $user = new Users;
        $user->username   = $this->request->getPost("username");
        $user->password   = password_hash($this->request->getPost("password"), PASSWORD_BCRYPT );
        $user->email      = $this->request->getPost("email");
        $user->permission = $this->request->getPost("permission");
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

      $flags['title']  = "Cadastrado com Sucesso!";
      $flags['text']   = "Membro cadastrado com sucesso!";

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $flags['status'],
      "title"     =>  $flags['title'],
      "text"      =>  $flags['text'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

  }

  public function NewDepartmentAction()
  {
    $this->response->setContentType("application/json");
    $flags = [
      'status'    => true,
      'title'     => false,
      'text'      => false,
      'redirect'  => '/team/departments',
      'time'      => 3200,
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

      $d = new Departments();
        $d->department = $this->request->getPost("department","string");
      $d->save();

      $name = $this->request->getPost("department","string");
      # Log What Happend
      $this->logManager($this->logs->create,"Cadastrou um novo departamento ({$name}).");

      $flags['title']  = "Cadastrado com Sucesso!";
      $flags['text']   = "Departamento cadastrado com sucesso! A página irá atualizar.";

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

  public function UpdateAction()
  {
    $this->response->setContentType("application/json");
    $flags = [
      'status'    => true,
      'title'     => false,
      'text'      => false,
    ];

    $u = Users::findFirstBy_($this->dispatcher->getParam("urlrequest"));
    $m = Team::findFirstByUid($u->_);

    if(!$this->request->isPost()):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Alterar!";
      $flags['text']   = "Metodo Inválido.";
    endif;

    if(!$this->security->checkToken()):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Alterar!";
      $flags['text']   = "Token de segurança inválido.";
    endif;

    if(!$this->isEmail($this->request->getPost("email"))):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Alterar!";
      $flags['text']   = "Endereço de E-Mail inválido!";
    endif;

    if( $this->request->getPost("email") != $u->email && Users::findFirstByEmail($this->request->getPost("email"))->_ != NULL ):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Alterar!";
      $flags['text']   = "Endereço de E-Mail já cadastrado!";
    endif;

    if( $this->request->getPost("username") != $u->username && Users::findFirstByUsername($this->request->getPost("username"))->_ != NULL ):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Alterar!";
      $flags['text']   = "Nome de usuário já cadastrado!";
    endif;

    if($flags['status']):

      $this->response->setStatusCode(200,"OK");

      if($this->request->hasFiles())
      {
        unlink("assets/manager/images/avtar/{$m->image}");
        foreach($this->request->getUploadedFiles() as $file)
        {
            $filename = substr(sha1(uniqid()), 0, 12).'.'.$file->getExtension();
            $file->moveTo("assets/manager/images/avtar/{$filename}");
        }
        $m->image = $filename;
      }
      else
      {
        $m->image = $m->image;
      }

        $u->username   = $this->request->getPost("username");
        $u->password   = ($this->request->getPost("password") != null ) ? password_hash($this->request->getPost("password"), PASSWORD_BCRYPT ) : $u->password;
        $u->email      = $this->request->getPost("email");
        $u->permission = $this->request->getPost("permission");
        $u->save();

        $m->name          = $this->request->getPost("name");
        $m->phone         = $this->request->getPost("phone");
        $m->department_id = $this->request->getPost("department");
        $m->save();

      $name = $this->request->getPost("name");
      # Log What Happend
      $this->logManager($this->logs->update,"Alterou informações de um membro ({$name}).");

      $flags['title']  = "Alterado com Sucesso!";
      $flags['text']   = "Informaçoes alteradas com sucesso!";

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $flags['status'],
      "title"     =>  $flags['title'],
      "text"      =>  $flags['text'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

  }

  public function UpdateDepartmentAction()
  {
    $this->response->setContentType("application/json");
    $flags = [
      'status'    => true,
      'title'     => false,
      'text'      => false,
      'redirect'  => '/team/departments',
      'time'      => 3200,
    ];

    if(!$this->request->isPost()):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Alterar!";
      $flags['text']   = "Metodo Inválido.";
    endif;

    if(!$this->security->checkToken()):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Alterar!";
      $flags['text']   = "Token de segurança inválido.";
    endif;

    if($flags['status']):
      $this->response->setStatusCode(200,"OK");

      $d = Departments::findFirst($this->dispatcher->getParam("urlrequest"));
        $d->department = $this->request->getPost("department","string");
      $d->save();

      $name = $this->request->getPost("department");
      # Log What Happend
      $this->logManager($this->logs->update,"Alterou nome de um departamento para ({$name}).");

      $flags['title']  = "Alterado com Sucesso!";
      $flags['text']   = "Departamento alterado com sucesso! A página irá atualizar.";

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
      $flags['title']  = "Erro ao Remover!";
      $flags['text']   = "Metodo Inválido.";
    endif;

    if(!$this->security->checkToken()):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Remover!";
      $flags['text']   = "Token de segurança inválido.";
    endif;

    if($this->request->getPost('select') === $this->dispatcher->getParam('urlrequest')):
      $flags['status']    = false ;
      $flags['title']     = "Atenção!";
      $flags['text']      = "É necessário selecionar um outro membro para assumir responsabilidade de todos os projetos que o mesmo seja responsável.";
    endif;


    if($flags['status']):

      $member = Team::findFirstByUid($this->dispatcher->getParam('urlrequest'));
      $user   = Users::findFirst($this->dispatcher->getParam('urlrequest'));

      # remove image from server
      unlink("assets/manager/images/avtar/{$member->image}");

      # update projects table

      # Log What Happend
      $this->logManager($this->logs->delete,"Removeu um membro da equipe ({$member->name}).");

      $member->delete();
      $user->delete();

      $flags['title']  = "Removido Com Successo";
      $flags['text']   = "Membro da equipe removido ";

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $flags['status'],
      "title"     =>  $flags['title'],
      "text"      =>  $flags['text'],
      "redirect"  =>  $flags['redirect'],
      "time"      =>  $flags['time'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
  }

  public function RemoveDepartmentAction()
  {
    $this->response->setContentType("application/json");
    $flags = [
      'status'    => true,
      'title'     => false,
      'text'      => false,
      'redirect'  => '/team/departments',
      'time'      => 0,
    ];

    if(!$this->request->isPost()):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Remover!";
      $flags['text']   = "Metodo Inválido.";
    endif;

    if(!$this->security->checkToken()):
      $flags['status'] = false ;
      $flags['title']  = "Erro ao Remover!";
      $flags['text']   = "Token de segurança inválido.";
    endif;

    if($this->request->getPost('departments') === $this->dispatcher->getParam('urlrequest')):
      $flags['status']    = false ;
      $flags['title']     = "Atenção!";
      $flags['text']      = "É necessário selecionar um outro membro para assumir responsabilidade de todos os projetos que o mesmo seja responsável.";
    endif;


    if($flags['status']):

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

      $flags['title']  = "Removido Com Successo";
      $flags['text']   = "Departamento da equipe removido ";

    endif;

    return $this->response->setJsonContent([
      "status"    =>  $flags['status'],
      "title"     =>  $flags['title'],
      "text"      =>  $flags['text'],
      "redirect"  =>  $flags['redirect'],
      "time"      =>  $flags['time'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
  }


}
