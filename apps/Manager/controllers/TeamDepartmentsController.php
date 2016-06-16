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

class TeamDepartmentsController extends ControllerBase
{
  private  $flags = [
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
    $this->view->pick("team/departments");
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

  public function DeleteAction()
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
