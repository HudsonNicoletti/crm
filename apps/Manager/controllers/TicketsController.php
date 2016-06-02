<?php

namespace Manager\Controllers;

use Manager\Models\Logs as Logs,
    Manager\Models\Tickets as Tickets,
    Manager\Models\TicketCategories as TicketCategories,
    Manager\Models\TicketsResponse as TicketsResponse;

class TicketsController extends ControllerBase
{

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
      ->addCss("assets/manager/css/app/email.css");

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
      ->execute();

      $this->view->ticket = $ticket[0];
      $this->view->tickets = $tickets;
    }
}
