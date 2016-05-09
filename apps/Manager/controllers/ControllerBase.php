<?php

namespace Manager\Controllers;

use Manager\Models\Users as Users,
    Manager\Models\Logs as Logs,
    Manager\Models\Team as Team;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
     public function initialize()
     {

        $this->assets
             ->addCss('http://fonts.googleapis.com/css?family=Raleway:400,500,600,700,300',false)
             ->addCss('assets/manager/css/bootstrap/bootstrap.css')
             ->addCss('assets/manager/css/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css')
             ->addCss('assets/manager/css/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.css')
             ->addCss('assets/manager/css/plugins/bootstrap-chosen/chosen.css')
             ->addCss('assets/manager/css/app/app.v1.css')
             ->addCss('assets/manager/css/app/custom.css');

        $this->assets
             ->addJs('assets/manager/js/jquery/jquery-1.9.1.min.js')
             ->addJs('assets/manager/js/plugins/underscore/underscore-min.js')
             ->addJs('assets/manager/js/bootstrap/bootstrap.min.js')
             ->addJs('assets/manager/js/globalize/globalize.min.js')
             ->addJs('assets/manager/js/plugins/nicescroll/jquery.nicescroll.min.js')
             ->addJs('assets/manager/js/moment/moment.js')
             ->addJs('assets/manager/js/plugins/inputmask/jquery.inputmask.bundle.js')
             ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
             ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js")
             ->addJs("assets/manager/js/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js")
             ->addJs("assets/manager/js/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js")
             ->addJs("assets/manager/js/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.min.js")
             ->addJs("assets/manager/js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.js")
             ->addJs("assets/manager/js/plugins/DevExpressChartJS/dx.chartjs.js")
             ->addJs("assets/manager/js/plugins/bootstrap-chosen/chosen.jquery.js")
             ->addJs("assets/manager/js/plugins/jquery.filtr.min.js")
             ->addJs('assets/manager/js/app/custom.js');

        # if session then set accessible vars
        if ($this->session->has("secure_id")):
          $this->view->user = Users::findFirst($this->session->get("secure_id"));
          $this->view->uinfo = Team::findFirstByUid($this->session->get("secure_id"));
        else:
          if($this->router->getControllerName() != 'login')
          {
            return $this->response->redirect("login");
          }
        endif;
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

    public function logManager($action,$desc)
    {
      $log = new Logs;
        $log->user        = $this->session->get("secure_id");
        $log->action      = $action;
        $log->date        = (new \DateTime())->format("Y-m-d H:i:s");
        $log->description = $desc;
      return ($log->save() ? true : false);
    }
}