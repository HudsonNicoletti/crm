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
}
