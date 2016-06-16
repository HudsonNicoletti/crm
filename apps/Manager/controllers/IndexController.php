<?php

namespace Manager\Controllers;

use Manager\Models\Logs as Logs,
    Manager\Models\Team as Team,
    Manager\Models\Tasks as Tasks,
    Manager\Models\Clients as Clients,
    Manager\Models\Assignments as Assignments;

class IndexController extends ControllerBase
{

    public function IndexAction()
    {
      // create a redirect for clients
      $this->assets
           ->addCss('assets/manager/css/plugins/calendar/calendar.css')
           ->addJs('assets/manager/js/plugins/underscore/underscore-min.js')
           ->addJs('assets/manager/js/plugins/calendar/calendar.js')
           ->addJs('assets/manager/js/plugins/calendar/calendar-conf.js');

      $user = $this->session->get("secure_id");
      $logs = Logs::query()
      ->columns([
        "Manager\Models\Team.name",
        "Manager\Models\Logs.action",
        "Manager\Models\Logs.date",
        "Manager\Models\Logs.description",
      ])
      ->innerJoin('Manager\Models\Team', 'Manager\Models\Logs.user = Manager\Models\Team.uid')
      ->orderBy("date DESC")
      ->limit(120)
      ->execute();

      $tasks = Assignments::query()->columns([
        "Manager\Models\Tasks._",
      ])
      ->innerJoin('Manager\Models\Tasks', 'Manager\Models\Assignments.project = Manager\Models\Tasks.project')
      ->where("Manager\Models\Assignments.member = '{$user}' AND Manager\Models\Tasks.status = 1")
      ->execute();

      $this->view->logs     = $logs;
      $this->view->tasks    = count($tasks);
      $this->view->clients  = Clients::find()->count();

    }

    public function CalendarAction()
    {
      $this->response->setContentType("application/json");

      $my = [];
      $colors = [
        "event-important",
        "event-warning",
        "event-info",
        "event-inverse",
        "event-success",
        "event-special",
      ];
      $tasks = Tasks::findByAssigned($this->session->get("secure_id"));
      foreach($tasks as $task)
      {
        array_push($my,[
          "id"    => $task->_,
          "title" => $task->title,
          "class" => $colors[rand(0,5)],
          "start" => (new \DateTime($task->created))->getTimestamp()."000",
          "end"   => (new \DateTime($task->deadline))->getTimestamp()."000",
        ]);
      }

      return $this->response->setJsonContent([
        "success"  => 1,
        "result"   => $my
      ]);

      # dates = timestamp of YYYY-DD-MM

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function webAction()
    {
      $this->response->setContentType("application/json");
      return $this->response->setJsonContent([
        $this->dispatcher->getParam("method"),
        $this->dispatcher->getParam("id"),
      ]);
      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}
