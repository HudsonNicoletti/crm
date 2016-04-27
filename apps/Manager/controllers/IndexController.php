<?php

namespace Manager\Controllers;

use Manager\Models\Logs as Logs;

use \Phalcon\Mvc\Model\Query\Builder as Builder;

class IndexController extends ControllerBase
{

    public function IndexAction()
    {
      // create a redirect for clients

      $params = [
         'models'     => ['Manager\Models\Logs'],
         'columns'    => ['Manager\Models\team.name','action','date','description'],
      ];
      $builder = new Builder($params);
      $builder->innerJoin('Manager\Models\team', 'Manager\Models\Logs.user = Manager\Models\team.uid');
      $builder->orderBy("date DESC");

      $this->view->logs = $this->modelsManager->executeQuery($builder->getPhql());

    }

    public function AuthAction()
    {
      if ($this->session->has("secure_id")):
        return $this->response->redirect("/index");
      else:
        return $this->response->redirect("/login");
      endif;
    }
}
