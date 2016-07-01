<?php

namespace Manager\Controllers;

use Manager\Models\Logs         as Logs,
    Manager\Models\Team         as Team,
    Manager\Models\Users        as Users,
    Manager\Models\Tasks        as Tasks,
    Manager\Models\Assignments  as Assignments,
    Manager\Models\Departments  as Departments;

use Mustache_Engine as Mustache;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Textarea,
    Phalcon\Forms\Element\Hidden;

class SettingsController extends ControllerBase
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
    $this->assets->addCss("assets/manager/css/app/email.css");
  }

  public function EmailAction()
  {
    $this->assets
    ->addCss("assets/manager/css/app/email.css")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

    $element['security'] = new Hidden( "security" ,[
      'name'  => $this->security->getTokenKey(),
      'value' => $this->security->getToken(),
    ]);

    $element['host'] = new Text( "host" ,[
      'class'         => "form-control",
      'title'         => "Host",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->host
    ]);

    $element['username'] = new Text( "username" ,[
      'class'         => "form-control",
      'title'         => "Username",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->username
    ]);

    $element['password'] = new Password( "password" ,[
      'class'         => "form-control",
      'title'         => "Password",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->password
    ]);

    $element['port'] = new Text( "port" ,[
      'class'         => "form-control",
      'title'         => "Port",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->port
    ]);

    $element['sender_email'] = new Text( "sender_email" ,[
      'class'         => "form-control",
      'title'         => "Sender E-Mail",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->email
    ]);

    $element['sender_name'] = new Text( "sender_name" ,[
      'class'         => "form-control",
      'title'         => "Sender Name",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->name
    ]);

    $element['level'] = new Select( "level" ,["tls" => "TLS","ssl" => "SSL"],[
      'class'         => "form-control chosen-select",
      'title'         => "Security Level",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->security
    ]);

    $form = new Form();
    foreach($element as $e)
    {
      $form->add($e);
    }
    $this->view->form = $form;
  }

  public function SaveEmailAction()
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

      $body = (new Mustache)->render(file_get_contents($_SERVER['DOCUMENT_ROOT']."/templates/config.tpl"),[
        'db_host'     => $this->configuration->database->host,
        'db_username' => $this->configuration->database->username,
        'db_password' => $this->configuration->database->password,
        'db_name'     => $this->configuration->database->dbname,
        'ma_host'     => $this->request->getPost("host","string"),
        'ma_username' => $this->request->getPost("username","string"),
        'ma_password' => $this->request->getPost("password","string"),
        'ma_security' => $this->request->getPost("level","string"),
        'ma_port'     => $this->request->getPost("port","int"),
        'ma_email'    => $this->request->getPost("sender_email","email"),
        'ma_name'     => $this->request->getPost("sender_name","string"),
      ]);

      file_put_contents($_SERVER['DOCUMENT_ROOT']."/config/config.ini",$body);

      $this->flags['title']  = "Alterado com Sucesso!";
      $this->flags['text']   = "Informaçoes alteradas com sucesso!";
      $this->flags['redirect']   = "/settings/email";
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

  public function AdminAction()
  {
    $this->assets
    ->addCss("assets/manager/css/app/email.css")
    ->addCss('assets/manager/css/plugins/bootstrap-chosen/chosen.css')
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js")
    ->addJs("assets/manager/js/plugins/bootstrap-chosen/chosen.jquery.js");

    $team = Users::query()
    ->columns([
      'Manager\Models\Users.permission',
      'Manager\Models\Users.email',
      'Manager\Models\Team.name',
      'Manager\Models\Team.uid',
      'Manager\Models\Team.image',
      'Manager\Models\Departments.department'
    ])
    ->innerJoin('Manager\Models\Team', 'Manager\Models\Team.uid = Manager\Models\Users._')
    ->innerJoin('Manager\Models\Departments', 'Manager\Models\Team.department_id = Manager\Models\Departments._')
    ->where("Manager\Models\Users.permission >= {$this->permissions->admin}")
    ->execute();

    $members = Users::query()
    ->columns([
      'Manager\Models\Team.name',
      'Manager\Models\Team.uid',
    ])
    ->innerJoin('Manager\Models\Team', 'Manager\Models\Team.uid = Manager\Models\Users._')
    ->where("Manager\Models\Users.permission < {$this->permissions->admin}")
    ->execute();

    $membersAvailable = [];

    foreach ($members as $member) {
      $membersAvailable[$member->uid] = $member->name;
    }

    $form = new Form();
    $form->add(new Hidden( "security" ,[
      'name'  => $this->security->getTokenKey(),
      'value' => $this->security->getToken(),
    ]));

    $form->add(new Select( "members" , $membersAvailable , [
      'class' => "chosen-select"
    ]));

    $this->view->form = $form;
    $this->view->members = $team;

  }

  public function AddAdminAction()
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

      $member = Users::findFirst($this->request->getPost("members"));
        $member->permission = $this->permissions->admin;
      $member->save();

      $this->flags['title']  = "Alterado com Sucesso!";
      $this->flags['text']   = "Permissões administrativas adicionadas com sucesso!";
      $this->flags['redirect']   = "/settings/admin";
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

  public function RemoveAdminAction()
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

      $member = Users::findFirst($this->dispatcher->getParam("uid"));
        $member->permission = $this->permissions->team;
      $member->save();

      $this->flags['title']  = "Alterado com Sucesso!";
      $this->flags['text']   = "Permissões administrativas removidas com sucesso!";
      $this->flags['redirect']   = "/settings/admin";
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

  public function ServerAction()
  {
    $this->assets
    ->addCss("assets/manager/css/app/email.css")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

    $form = new Form();

    $form->add(new Hidden( "security" ,[
      'name'  => $this->security->getTokenKey(),
      'value' => $this->security->getToken(),
    ]));

    $form->add(new Text( "host" ,[
      'class'         => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->database->host
    ]));

    $form->add(new Text( "username" ,[
      'class'         => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->database->username
    ]));

    $form->add(new Password( "password" ,[
      'class'         => "form-control",
      'value'         => $this->configuration->database->password
    ]));

    $form->add(new Text( "database" ,[
      'class'         => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->database->dbname
    ]));

    $this->view->form = $form;
  }

  public function SaveServerAction()
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

      $body = (new Mustache)->render(file_get_contents($_SERVER['DOCUMENT_ROOT']."/templates/config.tpl"),[
        'db_host'     => $this->request->getPost("host","string"),
        'db_username' => $this->request->getPost("username","string"),
        'db_password' => $this->request->getPost("password","string"),
        'db_name'     => $this->request->getPost("database","string"),
        'ma_host'     => $this->configuration->mail->host,
        'ma_username' => $this->configuration->mail->username,
        'ma_password' => $this->configuration->mail->password,
        'ma_security' => $this->configuration->mail->security,
        'ma_port'     => $this->configuration->mail->port,
        'ma_email'    => $this->configuration->mail->email,
        'ma_name'     => $this->configuration->mail->name,
      ]);

      file_put_contents($_SERVER['DOCUMENT_ROOT']."/config/config.ini",$body);

      $this->flags['title']  = "Alterado com Sucesso!";
      $this->flags['text']   = "Informaçoes alteradas com sucesso!";
      $this->flags['redirect']   = "/settings/server";
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

  public function TeamPermissionsAction()
  {

  }

  public function ClientPermissionsAction()
  {

  }
}
