<?php

namespace Manager\Controllers;

class IndexController extends ControllerBase
{

    public function IndexAction()
    {
      // create a redirect for clients
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
