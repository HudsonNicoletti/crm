<?php

namespace Manager\Controllers;

use Manager\Models\Logs         as Logs,
    Manager\Models\Projects     as Projects,
    Manager\Models\ProjectTypes as ProjectTypes;

use Mustache_Engine as Mustache;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Textarea,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\File,
    Phalcon\Forms\Element\Hidden;

use Phalcon\Mvc\Model\Query\Builder as Builder;

class ProjectCategoriesController extends ControllerBase
{
  private $flags = [
    'status'    => true,
    'title'     => false,
    'text'      => false,
    'redirect'  => false,
    'time'      => false,
    'target'    => false
  ];

  public function IndexAction()
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
    $this->view->pick("projects/categories");
  }

  public function NewAction()
  {
    $this->response->setContentType("application/json");
    # Target Response Box
    $this->flags['target']    = "#createBox";

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

      $title = $this->request->getPost("title","string");

      $category = new ProjectTypes;
        $category->title = $title;
      $category->save();

      # Log What Happend
      $this->logManager($this->logs->create,"Cadastrou uma categoria de projetos ( {$title} ).");

      $this->flags['status']    = true ;
      $this->flags['title']     = "Cadastrado com Sucesso!";
      $this->flags['text']      = "Categoria Cadastrada com sucesso!";
      $this->flags['redirect']  = "/project/categories";
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

  public function UpdateAction()
  {
    $this->response->setContentType("application/json");
    # Target Response Box
    $this->flags['target']    = "#updateBox";

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

      $title = $this->request->getPost("title","string");

      $category = ProjectTypes::findFirst($this->dispatcher->getParam("category"));
        $category->title = $title;
      $category->save();

      # Log What Happend
      $this->logManager($this->logs->update,"Alterou uma categoria de projetos ( {$title} ).");

      $this->flags['status']    = true ;
      $this->flags['title']     = "Alterado com Sucesso!";
      $this->flags['text']      = "Categoria ALterada com sucesso!";
      $this->flags['redirect']  = "/project/categories";
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

  public function DeleteAction()
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
      $c = $this->dispatcher->getParam("category");

      $category = ProjectTypes::findFirst($c);
        $title = $category->title;
      $category->delete();

      foreach(Projects::findByType($c) as $p)
      {
        $p->type = $this->request->getPost("category");
        $p->save();
      }

      # Log What Happend
      $this->logManager($this->logs->delete,"Removeu uma categoria de projetos ( {$title} ).");

      $this->flags['status']    = true ;
      $this->flags['title']     = "Removido com Sucesso!";
      $this->flags['text']      = "Categoria Removida com sucesso!";
      $this->flags['redirect']  = "/project/categories";
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
      $method = $this->dispatcher->getParam("method");
      $category = $this->dispatcher->getParam("category");

      # CREATING ELEMENTS
      if( $method == "remove" ):
        $element['category'] = new Select( "category" , ProjectTypes::find(["_ != '{$category}'"]) ,[
          'using'         => ["_","title"],
          'class'         => "form-control chosen-select",
          'title'         => "Categorias",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório"
        ]);
      else:
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
      if( $method == "create" ):
        $action = "/project/categories/new";
        $template = "create";
        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO UPDATE POPULATE WITH VALJUE TO ELEMENT
      elseif ($method == "modify"):
        $c = ProjectTypes::findFirst($category);
        $action = "/project/categories/update/{$category}";
        $template = "modify";

        $element['title']->setAttribute("value",$c->title);
        foreach($element as $e)
        {
          $form->add($e);
        }

      # IF REQUEST IS TO VIEW POPULATE WITH VALJUE TO ELEMENT
      elseif ($method == "remove"):
        $c = ProjectTypes::findFirst($category);
        $action = "/project/categories/delete/{$category}";
        $template = "remove";
        $alert = [
          "title" => "Selecione uma outra categoria para que seja transferidos os projetos."
        ];

        $element['category']->setAttribute("value",$category);
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
