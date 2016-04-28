<?php

namespace Manager\Controllers;

use \Manager\Models\Users as Users,
    \Manager\Models\Clients as Clients,
    \Manager\Models\Companies as Companies,
    \Manager\Models\ClientContacts as ClientContacts;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Hidden;

class ClientsController extends ControllerBase
{
    public function IndexAction()
    {

      $clients = Clients::query()
      ->columns([
        'Manager\Models\Clients._',
        'Manager\Models\Clients.firstname',
        'Manager\Models\Clients.lastname',
        'Manager\Models\Clients.document',
        'Manager\Models\Clients.phone',
        'Manager\Models\Clients.domain',
        'Manager\Models\Companies.fantasy',
      ])
      ->leftJoin('Manager\Models\Companies', 'Manager\Models\Companies.client = Manager\Models\Clients._')
      ->execute();

      $form = new Form();

        $form->add(new Hidden( "security" ,[
            'name'  => $this->security->getTokenKey(),
            'value' => $this->security->getToken(),
        ]));

      $this->view->form = $form;
      $this->view->clients = $clients;
    }

    public function CreateAction()
    {
      $form = new Form();

          $form->add(new Hidden( "security" ,[
              'name'  => $this->security->getTokenKey(),
              'value' => $this->security->getToken()
          ]));

          $form->add(new Text( "firstName" ,[
              'class'         => "form-control",
              'id'            => "firstName",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "lastName" ,[
              'class'         => "form-control",
              'id'            => "lastName",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "document" ,[
              'class'         => "form-control",
              'id'            => "document",
          ]));

          $form->add(new Text( "registration" ,[
              'class'         => "form-control",
              'id'            => "registration",
          ]));

          $form->add(new Text( "company" ,[
              'class'         => "form-control",
              'id'            => "company",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "fantasy" ,[
              'class'         => "form-control",
              'id'            => "fantasy",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "role" ,[
              'class'         => "form-control",
              'id'            => "role",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "email" ,[
              'class'         => "form-control",
              'id'            => "email",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
              'data-email'    => "* E-Mail Inválido",
          ]));

          $form->add(new Text( "phone" ,[
              'class'         => "form-control",
              'id'            => "phone",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "cellphone" ,[
              'class'         => "form-control",
              'id'            => "cellphone",
          ]));

          $form->add(new Text( "domain" ,[
              'class'         => "form-control",
              'id'            => "domain",
          ]));

          $form->add(new Text( "address" ,[
              'class'         => "form-control",
              'id'            => "address",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "district" ,[
              'class'         => "form-control",
              'id'            => "district",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "zip" ,[
              'class'         => "form-control",
              'id'            => "zip",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "city" ,[
              'class'         => "form-control",
              'id'            => "city",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "state" ,[
              'class'         => "form-control",
              'id'            => "state",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "username" ,[
              'class'         => "form-control",
              'id'            => "username",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Password( "password" ,[
              'class'         => "form-control",
              'id'            => "password",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "contact_name[]" ,[
              'class'         => "form-control",
          ]));

          $form->add(new Text( "contact_phone[]" ,[
              'class'         => "form-control",
          ]));

          $form->add(new Text( "contact_cellphone[]" ,[
              'class'         => "form-control",
          ]));

          $form->add(new Text( "contact_email[]" ,[
              'class'         => "form-control",
          ]));

          $form->add(new Text( "contact_area[]" ,[
              'class'         => "form-control",
          ]));

      $this->view->form = $form;
    }

    public function ModifyAction()
    {
      $urlrequest = $this->dispatcher->getParam('urlrequest');

      $client = Clients::query()
      ->columns([
        'Manager\Models\Clients.firstname',
        'Manager\Models\Clients.lastname',
        'Manager\Models\Clients.document',
        'Manager\Models\Clients.phone',
        'Manager\Models\Clients.cellphone',
        'Manager\Models\Clients.domain',
        'Manager\Models\Clients.address',
        'Manager\Models\Clients.district',
        'Manager\Models\Clients.zip',
        'Manager\Models\Clients.city',
        'Manager\Models\Clients.state',
        'Manager\Models\Companies.client',
        'Manager\Models\Companies.company',
        'Manager\Models\Companies.fantasy',
        'Manager\Models\Companies.registration',
        'Manager\Models\Companies.role',
        'Manager\Models\Users.email',
        'Manager\Models\Users.username',
      ])
      ->where("Manager\Models\Clients._ = '{$urlrequest}'")
      ->leftJoin('Manager\Models\Companies', 'Manager\Models\Companies.client = Manager\Models\Clients._')
      ->innerJoin('Manager\Models\Users', 'Manager\Models\Users._ = Manager\Models\Clients.user')
      ->execute();

      $form = new Form();

        $form->add(new Hidden( "security" ,[
            'name'  => $this->security->getTokenKey(),
            'value' => $this->security->getToken()
        ]));

        $form->add(new Text( "firstName" ,[
            'class'         => "form-control",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $client[0]->firstname
        ]));

        $form->add(new Text( "lastName" ,[
            'class'         => "form-control",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $client[0]->lastname
        ]));

        $form->add(new Text( "document" ,[
            'class'         => "form-control",
            'value'         => $client[0]->document
        ]));

        $form->add(new Text( "email" ,[
            'class'         => "form-control",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'data-email'    => "* E-Mail Inválido",
            'value'         => $client[0]->email
        ]));

        $form->add(new Text( "phone" ,[
            'class'         => "form-control",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'data-inputmask'=> "'alias' : 'phone'",
            'value'         => $client[0]->phone
        ]));

        $form->add(new Text( "cellphone" ,[
            'class'         => "form-control",
            'value'         => $client[0]->cellphone
        ]));

        $form->add(new Text( "domain" ,[
            'class'         => "form-control",
            'value'         => $client[0]->domain
        ]));

        $form->add(new Text( "address" ,[
            'class'         => "form-control",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $client[0]->address
        ]));

        $form->add(new Text( "district" ,[
            'class'         => "form-control",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $client[0]->district
        ]));

        $form->add(new Text( "zip" ,[
            'class'         => "form-control",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $client[0]->zip
        ]));

        $form->add(new Text( "city" ,[
            'class'         => "form-control",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $client[0]->city
        ]));

        $form->add(new Text( "state" ,[
            'class'         => "form-control",
            'id'            => "state",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $client[0]->state
        ]));

        $form->add(new Text( "username" ,[
            'class'         => "form-control",
            'id'            => "username",
            'value'         => $client[0]->username
        ]));

        $form->add(new Password( "password" ,[
            'class'         => "form-control",
            'id'            => "password",
        ]));

        if( $client[0]->client != null )
        {

          $form->add(new Text( "registration" ,[
              'class'         => "form-control",
              'value'         => $client[0]->registration
          ]));

          $form->add(new Text( "company" ,[
              'class'         => "form-control",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
              'value'         => $client[0]->company
          ]));

          $form->add(new Text( "fantasy" ,[
              'class'         => "form-control",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
              'value'         => $client[0]->fantasy
          ]));

          $form->add(new Text( "role" ,[
              'class'         => "form-control",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
              'value'         => $client[0]->role
          ]));

          $form->add(new Text( "contact_name[]" ,[
              'class'         => "form-control",
          ]));

          $form->add(new Text( "contact_phone[]" ,[
              'class'         => "form-control",
          ]));

          $form->add(new Text( "contact_cellphone[]" ,[
              'class'         => "form-control",
          ]));

          $form->add(new Text( "contact_email[]" ,[
              'class'         => "form-control",
          ]));

          $form->add(new Text( "contact_area[]" ,[
              'class'         => "form-control",
          ]));

        }

      switch ($client[0]->client) {
        case null:  $size = 6 ; break;
        default: $size = 3 ; break;
      }

      $this->view->form = $form;
      $this->view->uid  = $urlrequest;
      $this->view->size = $size;
      $this->view->contacts = ClientContacts::findByClient($urlrequest);
    }

    public function NewAction()
    {
      $this->response->setContentType("application/json");
      $flags = [
        'status'  => true,
        'title'   => false,
        'text'    => false
      ];

      if(!$this->request->isPost()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Token de segurança inválido.";
      endif;

      if(!$this->isEmail($this->request->getPost("email"))):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Endereço de E-Mail inválido!";
      endif;

      if( Users::findFirstByEmail($this->request->getPost("email"))->_ != NULL ):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Endereço de E-Mail já cadastrado!";
      endif;

      if( Users::findFirstByUsername($this->request->getPost("username"))->_ != NULL ):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Nome de usuário já cadastrado!";
      endif;

      if($flags['status']):
        $this->response->setStatusCode(200,"OK");

        $user = new Users;
          $user->username   = $this->request->getPost("username");
          $user->password   = password_hash($this->request->getPost("password"), PASSWORD_BCRYPT );
          $user->email      = $this->request->getPost("email");
          $user->permission = $this->permissions->client;
        $user->save();

        $client = new Clients;
          $client->user      = $user->_;
          $client->firstname = $this->request->getPost("firstName");
          $client->lastname  = $this->request->getPost("lastName");
          $client->document  = $this->request->getPost("document");
          $client->phone     = $this->request->getPost("phone");
          $client->cellphone = $this->request->getPost("cellphone");
          $client->domain    = $this->request->getPost("domain");
          $client->address   = $this->request->getPost("address");
          $client->district  = $this->request->getPost("district");
          $client->zip       = $this->request->getPost("zip");
          $client->city      = $this->request->getPost("city");
          $client->state     = $this->request->getPost("state");
        $client->save();

        if( $this->dispatcher->getParam("type") === "company" )
        {
          $company = new Companies;
            $company->client    = $client->_;
            $company->company   = $this->request->getPost("company");
            $company->fantasy   = $this->request->getPost("fantasy");
            $company->registration  = $this->request->getPost("registration");
            $company->role      = $this->request->getPost("role");
          $company->save();

          $m = array_combine(
            ['name','phone','cellphone','email','area'],
            [
              $this->request->getPost('contact_name'),
              $this->request->getPost('contact_phone'),
              $this->request->getPost('contact_cellphone'),
              $this->request->getPost('contact_email'),
              $this->request->getPost('contact_area')
            ]
          );

          for($i=0; $i < count($m['name']); $i++)
          {
            $contact = new ClientContacts;
              $contact->client = $company->_;
              $contact->name  = $m['name'][$i];
              $contact->phone = $m['phone'][$i];
              $contact->cellphone = $m['cellphone'][$i];
              $contact->email = $m['email'][$i];
              $contact->area  = $m['area'][$i];
            $contact->save();
          }

        }

        $name = $this->request->getPost("firstName")." ".$this->request->getPost("lastName");
        # Log What Happend
        $this->logManager($this->logs->create,"Cadastrou um novo cliente ({$name}).");

        $flags['status'] = true ;
        $flags['title']  = "Cadastrado com Sucesso!!";
        $flags['text']   = "Cliente Cadastrado com sucesso!";

      endif;

      return $this->response->setJsonContent([
        "status" => $flags['status'] ,
        "title"  => $flags['title'] ,
        "text"   => $flags['text']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function UpdateAction()
    {
      $this->response->setContentType("application/json");
      $flags = [
        'status'  => true,
        'title'   => false,
        'text'    => false
      ];

      $c = Clients::findFirst($this->dispatcher->getParam("urlrequest"));
      $company = Companies::findFirstByClient($c->_);
      $u = Users::findFirst($c->user);

      if(!$this->request->isPost()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Token de segurança inválido.";
      endif;

      if(!$this->isEmail($this->request->getPost("email"))):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Endereço de E-Mail inválido!";
      endif;

      if( $this->request->getPost("email") != $u->email && Users::findFirstByEmail($this->request->getPost("email"))->_ != NULL ):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Endereço de E-Mail já cadastrado!";
      endif;

      if( $this->request->getPost("username") != $u->username && Users::findFirstByusername($this->request->getPost("username"))->_ != NULL ):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Nome de usuário já cadastrado!";
      endif;

      if($flags['status']):
        $this->response->setStatusCode(200,"OK");

          $u->username   = $this->request->getPost("username");
          $u->password   = ($this->request->getPost("password") != null ) ? password_hash($this->request->getPost("password"), PASSWORD_BCRYPT ) : $u->password;
          $u->name       = $this->request->getPost("firstName");
          $u->email      = $this->request->getPost("email");
          $u->permission = $this->permissions->client;
        $u->save();

          $c->firstname = $this->request->getPost("firstName");
          $c->lastname  = $this->request->getPost("lastName");
          $c->document  = $this->request->getPost("document");
          $c->phone     = $this->request->getPost("phone");
          $c->cellphone = $this->request->getPost("cellphone");
          $c->domain    = $this->request->getPost("domain");
          $c->address   = $this->request->getPost("address");
          $c->district  = $this->request->getPost("district");
          $c->zip       = $this->request->getPost("zip");
          $c->city      = $this->request->getPost("city");
          $c->state     = $this->request->getPost("state");
        $c->save();

        if( $company->_ != null )
        {
            $company->company   = $this->request->getPost("company");
            $company->fantasy   = $this->request->getPost("fantasy");
            $company->registration  = $this->request->getPost("registration");
            $company->role      = $this->request->getPost("role");
          $company->save();

          foreach( ClientContacts::findByClient($c->_) as $contact ): $contact->delete(); endforeach;

          $m = array_combine(
            ['name','phone','cellphone','email','area'],
            [
              $this->request->getPost('contact_name'),
              $this->request->getPost('contact_phone'),
              $this->request->getPost('contact_cellphone'),
              $this->request->getPost('contact_email'),
              $this->request->getPost('contact_area')
            ]
          );

          for($i=0; $i < count($m['name']); $i++)
          {
            $contact = new ClientContacts;
              $contact->client = $c->_;
              $contact->name  = $m['name'][$i];
              $contact->phone = $m['phone'][$i];
              $contact->cellphone = $m['cellphone'][$i];
              $contact->email = $m['email'][$i];
              $contact->area  = $m['area'][$i];
            $contact->save();
          }

        }

        $name = $this->request->getPost("firstName")." ".$this->request->getPost("lastName");
        # Log What Happend
        $this->logManager($this->logs->update,"Alterou informações de um cliente ({$name}).");

        $flags['status'] = true ;
        $flags['title']  = "Alterado com Sucesso!!";
        $flags['text']   = "Cliente Alterado com sucesso!";

      endif;

      return $this->response->setJsonContent([
        "status" => $flags['status'] ,
        "title"  => $flags['title'] ,
        "text"   => $flags['text']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function RemoveAction()
    {
      $this->response->setContentType("application/json");
      $flags = [
        'status'    => true,
        'title'     => false,
        'text'      => false,
        'redirect'  => false,
        'time'      => 0,
      ];

      if(!$this->request->isPost()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $flags['status'] = false ;
        $flags['title']  = "Erro ao Cadastrar!";
        $flags['text']   = "Token de segurança inválido.";
      endif;

      if($flags['status']):

        $c = Clients::findFirst($this->dispatcher->getParam("urlrequest"));
        $u = Users::findFirst($c->user);
        $y = Companies::findFirstByClient($c->_);
        $n = "{$c->firstname} {$c->lastname}";
          $c->delete();
          $u->delete();

        if( $y->_ != null )
        {
          $y->delete();

          foreach( ClientContacts::findByClient($c->_) as $client )
          {
            $client->delete();
          }
        }

        # Log What Happend
        $this->logManager($this->logs->delete,"Removeu um cliente ({$n}).");

        $flags['title']     = "Removido Com Sucesso!";
        $flags['text']      = "Cliente Removido com Sucesso.";
        $flags['redirect']  = "/clients";
        $flags['time']      = 1000;

      endif;

      return $this->response->setJsonContent([
        "status"    =>  $flags['status'],
        "title"     =>  $flags['title'],
        "text"      =>  $flags['text'],
        "redirect"  =>  $flags['redirect'],
        "time"      =>  $flags['time']
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

}
