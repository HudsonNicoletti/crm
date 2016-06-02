<?php

namespace Manager\Controllers;

use Mustache_Engine as Mustache;

use Manager\Models\Users as Users,
    Manager\Models\Clients as Clients,
    Manager\Models\Logs as Logs,
    Manager\Models\Team as Team;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function Initialize()
    {
      $this->assets
           ->addCss('http://fonts.googleapis.com/css?family=Raleway:400,500,600,700,300',false)
           ->addCss('assets/manager/css/bootstrap/bootstrap.css')
           ->addCss('assets/manager/css/app/app.v1.css')
           ->addCss('assets/manager/css/app/custom.css');

      $this->assets
           ->addJs('assets/manager/js/jquery/jquery-1.9.1.min.js')
           ->addJs('assets/manager/js/bootstrap/bootstrap.min.js')
           ->addJs('assets/manager/js/globalize/globalize.min.js')
           ->addJs('assets/manager/js/plugins/nicescroll/jquery.nicescroll.min.js')
           ->addJs('assets/manager/js/app/custom.js');

      if($this->session->has("secure_id"))
      {
        if($this->router->getControllerName() == 'login'):
          return $this->response->redirect("/index");
        endif;

        $id = $this->session->get("secure_id");
        $user = Users::findFirst($id);
        $info = ( $user->permission == $this->permissions->client ? Clients::findFirstByUser($id) : Team::findFirstByUid($id) );

        if($user->permission >= $this->permissions->team)
        {
          $nav = [
            ["active" => ($this->router->getControllerName() == "index"    ? "active" : ""), "href" => "/",         "icon" => "bookmark-o",     "label" => "Visão Geral"],
            ["active" => ($this->router->getControllerName() == "tasks"    ? "active" : ""), "href" => "/tasks",    "icon" => "check-square-o", "label" => "Tarefas"],
            ["active" => ($this->router->getControllerName() == "tickets"  ? "active" : ""), "href" => "/tickets",  "icon" => "ticket",         "label" => "Chamados"],
            ["active" => ($this->router->getControllerName() == "team"     ? "active" : ""), "href" => "/team",     "icon" => "users",          "label" => "Equipe"],
            ["active" => ($this->router->getControllerName() == "clients"  ? "active" : ""), "href" => "/clients",  "icon" => "briefcase",      "label" => "Clientes"],
            ["active" => ($this->router->getControllerName() == "projects" ? "active" : ""), "href" => "/projects", "icon" => "archive",        "label" => "Projetos"],
            ["active" => ($this->router->getControllerName() == "finance"  ? "active" : ""), "href" => "/finance",  "icon" => "credit-card",    "label" => "Financeiro"],
            ["active" => ($this->router->getControllerName() == "settings" ? "active" : ""), "href" => "/settings", "icon" => "wrench",         "label" => "Configurações"],
          ];
        }
        else
        {
          $nav = [
            ["active" => ($this->router->getControllerName() == "index"    ? "active" : ""), "href" => "/",         "icon" => "bookmark-o",     "label" => "Visão Geral"],
            ["active" => ($this->router->getControllerName() == "tickets"  ? "active" : ""), "href" => "/tickets",  "icon" => "ticket",         "label" => "Chamados"],
          ];
        }

        $this->view->user = $user;
        $this->view->uinfo = $info;
        $this->view->navigation  = (new Mustache)->render(file_get_contents($_SERVER['DOCUMENT_ROOT']."/templates/navigation.tpl"),[ 'nav' => $nav ]);
      }
      else
      {
        if($this->router->getControllerName() != 'login'):
          return $this->response->redirect("/login");
        endif;
      }
    }

    public function URLGenerator($str)
    {
      setlocale(LC_ALL, 'en_US.UTF8');
      $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
      $clean = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $clean);
      $clean = strtolower(trim($clean, '-'));
      $clean = preg_replace("/[\/_| -]+/", '-', $clean);

      return $clean;
    }

    public function isEmail($str)
    {
      return preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $str );
    }

    public function logManager($action,$desc,$project = null)
    {
      $log = new Logs;
        $log->user        = $this->session->get("secure_id");
        $log->project     = $project;
        $log->action      = $action;
        $log->date        = (new \DateTime())->format("Y-m-d H:i:s");
        $log->description = $desc;
      return ($log->save() ? true : false);
    }
}
