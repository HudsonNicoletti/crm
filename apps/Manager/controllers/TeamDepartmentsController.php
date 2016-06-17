<?php

namespace Manager\Controllers;

use Manager\Models\Team as Team,
    Manager\Models\Users as Users,
    Manager\Models\Tasks        as Tasks,
    Manager\Models\Assignments  as Assignments,
    Manager\Models\Departments as Departments;

use Mustache_Engine as Mustache;

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

      $d = new Departments();
        $d->department = $this->request->getPost("title","string");
      $d->save();

      $name = $this->request->getPost("title","string");
      # Log What Happend
      $this->logManager($this->logs->create,"Cadastrou um novo departamento ( {$name} ).");

      $this->flags['title']  = "Cadastrado com Sucesso!";
      $this->flags['text']   = "Departamento cadastrado com sucesso! A página irá atualizar.";
      $this->flags['redirect']   = "/team/departments";
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

      $d = Departments::findFirst($this->dispatcher->getParam("department"));
        $d->department = $this->request->getPost("title","string");
      $d->save();

      $name = $this->request->getPost("title");
      # Log What Happend
      $this->logManager($this->logs->update,"Alterou nome de um departamento para ( {$name} ).");

      $this->flags['title']  = "Alterado com Sucesso!";
      $this->flags['text']   = "Departamento alterado com sucesso! A página irá atualizar.";
      $this->flags['redirect']   =  '/team/departments';
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

      $d = Departments::findFirst($this->dispatcher->getParam('department'));

      $m = Team::findByDepartment_id($d->_);
      foreach($m as $t)
      {
        $t->department_id = $this->request->getPost("department");
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
      $id = $this->dispatcher->getParam("department");

      if($this->dispatcher->getParam("method") == "remove"):
        $element['department'] = new Select( "department" , Departments::find([" _ != '{$id}' "]) ,[
          'using' =>  ['_','department'],
          'title' => "Departamento",
          'class' => "chosen-select form-control"
        ]);
      else:
        # CREATING ELEMENTS
        $element['title'] = new Text( "title" ,[
          'class'         => "form-control",
          'title'         => "Título",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório"
        ]);
      endif;

      $element['security'] = new Hidden( "security" ,[
          'name'  => $this->security->getTokenKey(),
          'value' => $this->security->getToken(),
      ]);

      # IF REQUEST IS TO CREATE JUST POPULATE WITH DEFAULT ELEMENTS
      if( $this->dispatcher->getParam("method") == "create" ):
        $action = "/team/department/new";
        $template = "create";

        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO UPDATE POPULATE WITH VALJUE TO ELEMENT
      elseif ($this->dispatcher->getParam("method") == "modify"):
        $department = Departments::findFirst($id);
        $action = "/team/department/update/{$department->_}";
        $template = "modify";

        $element['title']->setAttribute("value",$department->department);
        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO VIEW POPULATE WITH VALJUE TO ELEMENT
      elseif ($this->dispatcher->getParam("method") == "view"):
        $department = Departments::findFirst($id);
        $template = "view";

        $element['title']->setAttribute("value",$department->department);
        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO REMOVE POPULATE WITH VALJUE TO ELEMENT
      elseif ($this->dispatcher->getParam("method") == "remove"):
        $department = Departments::findFirst($id);
        $action = "/team/department/delete/{$department->_}";
        $alert = [
          "title" => "Selecione Um Departamento!",
          "desc"  => "É necessário que selecione um departamento para que todos os membros deste departamento sejam transferidos."
        ];
        $template = "remove";

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
