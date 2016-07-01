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

use Mustache_Engine as Mustache;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Textarea,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\File,
    Phalcon\Forms\Element\Hidden;

use Phalcon\Mvc\Model\Query\Builder as Builder;

class ProjectMembersController extends ControllerBase
{

  private $flags = [
    'status'    => true,
    'title'     => false,
    'text'      => false,
    'redirect'  => false,
    'time'      => false,
    'target'    => false
  ];

  public function NewAction()
  {
    $this->response->setContentType("application/json");

    $this->flags['target'] = "#createBox";

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

      $project = $this->dispatcher->getParam("project");
      $member = $this->request->getPost("assign");

      $assign = new Assignments;
        $assign->project = $project;
        $assign->member  = $member;
      $assign->save();

      $member = Team::findFirstByUid($member)->name;
      $name = Projects::findFirst($project)->title;

      # Log What Happend
      $this->logManager($this->logs->update,"Adicionou o membro {$member}",$project);

      $this->flags['status'] = true ;
      $this->flags['title']  = "Adicionado Com Sucesso!";
      $this->flags['text']   = "Membro adicionado com sucesso ao projeto!";
      $this->flags['redirect']   = "/project/{$project}/overview";
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

  public function DeleteAction()
  {
    $this->response->setContentType("application/json");

    $this->flags['target'] = "#removeBox";

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

      $project = $this->dispatcher->getParam("project");
      $member = $this->dispatcher->getParam("member");
      $assign = $this->dispatcher->getParam("assign");

      foreach(Assignments::find(["project = '{$project}' AND member = '{$member}'"]) as $assign)
      {
        $assign->delete();
      }
      foreach(Tasks::find(["project = '{$project}' AND assigned = '{$member}'"]) as $task)
      {
        $task->assigned = $assign;
        $task->save();
      }

      $member = Team::findFirstByUid($member)->name;
      $name = Projects::findFirst($project)->title;

      # Log What Happend
      $this->logManager($this->logs->delete,"Removeu o membro {$member}",$project);

      $this->flags['status'] = true ;
      $this->flags['title']  = "Removido Com Sucesso!";
      $this->flags['text']   = "Membro removido com sucesso do projeto!";
      $this->flags['redirect']   = "/project/{$project}/overview";
      $this->flags['time']   = 1200;

    endif;

    return $this->response->setJsonContent([
      "status"    => $this->flags['status'] ,
      "title"     => $this->flags['title'] ,
      "text"      => $this->flags['text'],
      "redirect"  => $this->flags['redirect'] ,
      "time"      => $this->flags['time'],
      "target"    => $this->flags['target'],
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
      $alert  = false;
      $inputs = [];
      $assigned = [];
      $method = $this->dispatcher->getParam("method");
      $project = $this->dispatcher->getParam("project");
      $member = $this->dispatcher->getParam("member");

      $assignedMembers = Assignments::findByProject($project);
      foreach($assignedMembers as $i => $a)
      {
        if( $method == "create" ):
          array_push($assigned, "uid != '{$a->member}' AND ");
        endif;
        if( $method == "remove" ):
          if($a->member != $member)
          {
            array_push($assigned, "uid = '{$a->member}' OR ");
          }
        endif;
      }
      $assigned = ( $method == "create" ) ? trim(implode(" ",$assigned)," AND ") : trim(implode(" ",$assigned)," OR ");

      $available = Team::find([$assigned]);

      # CREATING ELEMENTS
      $element['assign'] = new Select( "assign" , $available ,[
        'using' =>  ['uid','name'],
        'title' => "Membros",
        'class' => "chosen-select form-control"
      ]);

      $element['security'] = new Hidden( "security" ,[
        'name'  => $this->security->getTokenKey(),
        'value' => $this->security->getToken(),
      ]);

      # IF REQUEST IS TO CREATE JUST POPULATE WITH DEFAULT ELEMENTS
      if( $method == "create" ):
        if(count($available) == 0)
        {
          $alert = [ "title" => "Atenção !", "desc" => "Todos os membros já estão participando deste projeto." ];
          $template = "view";
        }
        else
        {
          $action = "/project/{$project}/member/new";
          $template = "create";
          foreach($element as $e)
          {
            $form->add($e);
          }
        }

      # IF REQUEST IS TO UPDATE POPULATE WITH VALJUE TO ELEMENT
      elseif ($method == "remove"):
        $action = "/project/{$project}/member/delete/{$member}";
        $template = "remove";
        $alert = [ "title" => "Atenção !", "desc" => "Selecione um membro participante para que todas as tarefas designadas sejam transferidos." ];

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
