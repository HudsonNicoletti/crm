<?php

namespace Manager\Controllers;

use Manager\Models\Logs as Logs;

use Mustache_Engine as Mustache;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\Password,
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
    $form = new Form();

    $form->add(new Hidden( "security" ,[
      'name'  => $this->security->getTokenKey(),
      'value' => $this->security->getToken(),
    ]));

    $this->view->form = $form;
  }

  public function EmailAction()
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
      'value'         => $this->configuration->mail->host
    ]));

    $form->add(new Text( "username" ,[
      'class'         => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->username
    ]));

    $form->add(new Password( "password" ,[
      'class'         => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->password
    ]));

    $form->add(new Text( "port" ,[
      'class'         => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->port
    ]));

    $form->add(new Text( "sender_email" ,[
      'class'         => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->email
    ]));

    $form->add(new Text( "sender_name" ,[
      'class'         => "form-control",
      'data-validate' => true,
      'data-empty'    => "* Campo Obrigatório",
      'value'         => $this->configuration->mail->name
    ]));

    $form->add(new Select("level",[
      "tls" => "TLS",
      "ssl" => "SSL"
    ],[
      'class' => "form-control" ,
      'value' => $this->configuration->mail->security
    ]));

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
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
    ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

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

}
