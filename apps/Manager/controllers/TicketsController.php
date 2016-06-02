<?php

namespace Manager\Controllers;

use Manager\Models\Logs as Logs,
    Manager\Models\Tickets as Tickets,
    Manager\Models\TicketCategories as TicketCategories,
    Manager\Models\TicketsResponse as TicketsResponse;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Hidden;

class TicketsController extends ControllerBase
{
    private  $flags = [
      'status'    => true,
      'title'     => false,
      'text'      => false,
      'redirect'  => false,
      'time'      => null
    ];

    public function IndexAction()
    {

      $tickets = Tickets::query()
      ->columns([
        'Manager\Models\Tickets._',
        'Manager\Models\Tickets.title',
        'Manager\Models\Tickets.updated',
        'Manager\Models\Tickets.status',
        'Manager\Models\TicketCategories.category',
        'Manager\Models\Clients.firstname',
        'Manager\Models\Clients.lastname',
        'Manager\Models\Companies.fantasy',
      ])
      ->innerJoin('Manager\Models\TicketCategories', 'Manager\Models\Tickets.category = Manager\Models\TicketCategories._')
      ->innerJoin('Manager\Models\Clients', 'Manager\Models\Clients.user = Manager\Models\Tickets.user')
      ->leftJoin('Manager\Models\Companies', 'Manager\Models\Companies.client = Manager\Models\Clients._')
      ->execute();

      $this->view->tickets = $tickets;
    }

    public function ViewAction()
    {

      $this->assets
      ->addCss("assets/manager/css/app/email.css")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

      $ticket = Tickets::query()
      ->columns([
        'Manager\Models\Tickets._',
        'Manager\Models\Tickets.title',
        'Manager\Models\Tickets.created',
        'Manager\Models\Tickets.updated',
        'Manager\Models\Tickets.status',
        'Manager\Models\Projects.title as project',
        'Manager\Models\TicketCategories.category',
      ])
      ->innerJoin('Manager\Models\TicketCategories', 'Manager\Models\Tickets.category = Manager\Models\TicketCategories._')
      ->leftJoin('Manager\Models\Projects', 'Manager\Models\Projects._ = Manager\Models\Tickets.project')
      ->where("Manager\Models\Tickets._ = :ticket:")
      ->bind([
        "ticket" => $this->dispatcher->getParam("ticket")
      ])
      ->execute();

      $tickets = TicketsResponse::query()
      ->columns([
        'Manager\Models\TicketsResponse.text',
        'Manager\Models\TicketsResponse.file',
        'Manager\Models\TicketsResponse.date',
        'Manager\Models\Clients.firstname',
        'Manager\Models\Clients.lastname',
        'Manager\Models\Clients.image as clientImage',
        'Manager\Models\Companies.fantasy',
        'Manager\Models\Team.name as team',
        'Manager\Models\Team.image as teamImage',
      ])
      ->leftJoin('Manager\Models\Clients', 'Manager\Models\Clients.user = Manager\Models\TicketsResponse.user')
      ->leftJoin('Manager\Models\Companies', 'Manager\Models\Companies.client = Manager\Models\Clients._')
      ->leftJoin('Manager\Models\Team', 'Manager\Models\Team.uid = Manager\Models\TicketsResponse.user')
      ->where("Manager\Models\TicketsResponse.ticket = :ticket:")
      ->bind([
        "ticket"  =>  $this->dispatcher->getParam("ticket")
      ])
      ->orderBy("date ASC")
      ->execute();

      $form = new Form();

      $form->add(new Hidden( "security" ,[
        'name'  => $this->security->getTokenKey(),
        'value' => $this->security->getToken(),
      ]));

      $form->add(new Text( "text" ,[
        'class'  => "form-control"
      ]));

      $this->view->form = $form;
      $this->view->ticket = $ticket[0];
      $this->view->tickets = $tickets;
    }

    public function SendAction()
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

        $ticket = $this->dispatcher->getParam("ticket");

          $response = new TicketsResponse;
            $response->ticket = $ticket;
            $response->user = $this->session->get("secure_id");
            $response->text = $this->request->getPost("text");
            $response->date = (new \DateTime())->format("Y-m-d H:i:s");
          $response->save();

        # Log What Happend
        $this->logManager($this->logs->update,"Respondeu o chamado #({$ticket}).");

        $this->flags['title']     = "Enviado Com Sucesso!";
        $this->flags['text']      = "Mensagem enviado com Sucesso.";
        $this->flags['redirect']  = "/tickets/view/{$ticket}";
        $this->flags['time']      = 1200;

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
}
