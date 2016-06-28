<?php

namespace Manager\Controllers;

use Manager\Models\Team         as Team,
    Manager\Models\Users        as Users,
    Manager\Models\Tasks        as Tasks,
    Manager\Models\Assignments  as Assignments,
    Manager\Models\Departments  as Departments;

use Mustache_Engine as Mustache;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\File,
    Phalcon\Forms\Element\Hidden;

use \Phalcon\Mvc\Model\Query\Builder as Builder;

class TeamController extends ControllerBase
{
  private $flags = [
    'status'    => true,
    'title'     => false,
    'text'      => false,
    'redirect'  => false,
    'time'      => null,
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

      if($this->request->hasFiles()):
        foreach($this->request->getUploadedFiles() as $file):
          $filename = substr(sha1(uniqid()), 0, 12).'.'.$file->getExtension();
          $file->moveTo("assets/manager/images/avtar/{$filename}");
        endforeach;
      else:
        $filename = null;
      endif;

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
      "target"    =>  $this->flags['target'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

  }

  public function UpdateAction()
  {
    $this->response->setContentType("application/json");

    $u = Users::findFirstBy_($this->dispatcher->getParam("member"));
    $m = Team::findFirstByUid($u->_);

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
      "target"    =>  $this->flags['target'],
    ]);

    $this->response->send();
    $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

  }

  public function DeleteAction()
  {
    $this->response->setContentType("application/json");

    $this->flags['target'] = "#removeBox";


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

      $member = Team::findFirstByUid($this->dispatcher->getParam('member'));
      $user   = Users::findFirst($member->uid);

      # remove image from server
      unlink("assets/manager/images/avtar/{$member->image}");

      # update projects assignments table
      foreach (Assignments::findByMember($member->uid) as $assign)
      {
        $assign->delete();
      }
      foreach (Tasks::findByAssigned($member->uid) as $task)
      {
        $task->assigned = $this->request->getPost("member");
        $task->save();
      }

      # Log What Happend
      $this->logManager($this->logs->delete,"Removeu um membro da equipe ( {$member->name} ).");

      $member->delete();
      $user->delete();

      $this->flags['title']      = "Removido Com Successo";
      $this->flags['text']       = "Membro da equipe removido ";
      $this->flags['redirect']   = "/team";
      $this->flags['time']       = 1200;

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
      $alert = false;
      $inputs = [];
      $uid = $this->dispatcher->getParam("member");

      if($uid)
      {
        $member = Users::query()
        ->columns([
          'Manager\Models\Users._',
          'Manager\Models\Users.email',
          'Manager\Models\Users.username',
          'Manager\Models\Team.name',
          'Manager\Models\Team.department_id as department',
          'Manager\Models\Team.phone'
        ])
        ->innerJoin('Manager\Models\Team', 'Manager\Models\Team.uid = Manager\Models\Users._')
        ->innerJoin('Manager\Models\Departments', 'Manager\Models\Team.department_id = Manager\Models\Departments._')
        ->where("Manager\Models\Users._ = :user:")
        ->bind([
          "user"  => $uid
        ])
        ->execute();
      }

      if($this->dispatcher->getParam("method") == "remove"):
        $element['member'] = new Select( "member" , Team::find([" uid != '{$uid}' "]) ,[
          'using' =>  ['_','name'],
          'title' => "Membros",
          'class' => "chosen-select form-control"
        ]);
      else:

      # CREATING ELEMENTS
      $element['name'] = new Text( "name" ,[
        'class'         => "form-control",
        'title'         => "Nome Completo",
        'data-validate' => true,
        'data-empty'    => "* Campo Obrigatório"
      ]);

      $element['email'] = new Text( "email" ,[
        'class'         => "form-control",
        'title'         => "E-Mail",
        'data-validate' => true,
        'data-empty'    => "* Campo Obrigatório"
      ]);

      $element['phone'] = new Text( "phone" ,[
        'class'         => "form-control",
        'title'         => "Telefone",
      ]);

      $element['department'] = new Select( "department" , Departments::find() ,[
        'using' =>  ['_','department'],
        'title' => "Departamento",
        'class' => "chosen-select form-control"
      ]);

      if($this->dispatcher->getParam("method") != "view"):
      $element['image'] = new File( "image" ,[
        'class'         => "form-control",
        'title'         => "Foto",
      ]);
      endif;

      $element['username'] = new Text( "username" ,[
        'class'         => "form-control",
        'title'         => "Usuário",
        'data-validate' => true,
        'data-empty'    => "* Campo Obrigatório"
      ]);

      if($this->dispatcher->getParam("method") != "view"):
      $element['password'] = new Password( "password" ,[
        'class'         => "form-control",
        'title'         => "Senha",
      ]);
      endif;

      endif;

      $element['security'] = new Hidden( "security" ,[
          'name'  => $this->security->getTokenKey(),
          'value' => $this->security->getToken(),
      ]);

      # IF REQUEST IS TO CREATE JUST POPULATE WITH DEFAULT ELEMENTS
      if( $this->dispatcher->getParam("method") == "create" ):
        $action = "/team/new";
        $template = "create";

        $element['password']    ->setAttribute("data-validate",true)->setAttribute("data-empty","* Campo Obrigatório");
        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO UPDATE POPULATE WITH VALJUE TO ELEMENT
      elseif ($this->dispatcher->getParam("method") == "modify"):
        $action = "/team/update/{$member[0]->_}";
        $template = "modify";

        $element['name']        ->setAttribute("value",$member[0]->name);
        $element['email']       ->setAttribute("value",$member[0]->email);
        $element['phone']       ->setAttribute("value",$member[0]->phone);
        $element['department']  ->setAttribute("value",$member[0]->department);
        $element['username']    ->setAttribute("value",$member[0]->username);
        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO DELETE POPULATE WITH VALJUE TO ELEMENT
      elseif ($this->dispatcher->getParam("method") == "remove"):
        $action = "/team/delete/{$member[0]->_}";
        $template = "remove";

        $alert = [
          "title" => "Selecione Um Membro!",
          "desc"  => "É necessário que selecione um membro para que todos os projetos e tarefas deste membro sejam transferidos."
        ];
        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO VIEW POPULATE WITH VALJUE TO ELEMENT
      elseif ($this->dispatcher->getParam("method") == "view"):
        $template = "view";

        $element['name']        ->setAttribute("disabled",true)->setAttribute("value",$member[0]->name);
        $element['email']       ->setAttribute("disabled",true)->setAttribute("value",$member[0]->email);
        $element['phone']       ->setAttribute("disabled",true)->setAttribute("value",$member[0]->phone);
        $element['department']  ->setAttribute("disabled",true)->setAttribute("value",$member[0]->department);
        $element['username']    ->setAttribute("disabled",true)->setAttribute("value",$member[0]->username);
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
        "alert"   => $alert,
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
