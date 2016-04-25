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
      $form = new Form();

        $form->add(new Hidden( "security" ,[
            'name'  => $this->security->getTokenKey(),
            'value' => $this->security->getToken(),
        ]));

      $this->view->form = $form;
      $this->view->clients = Clients::find();
      $this->view->companies = Companies::find();
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
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
          ]));

          $form->add(new Text( "registration" ,[
              'class'         => "form-control",
              'id'            => "registration",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
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
              'data-inputmask'=> "'alias' : 'phone'"
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

          $form->add(new Text( "contact_area[]" ,[
              'class'         => "form-control",
          ]));

      $this->view->form = $form;
    }

    public function ModifyAction()
    {
      $type = $this->dispatcher->getParam("type");
      $urlrequest = $this->dispatcher->getParam("urlrequest");

      ( $type === 'person' ) ? $c = Clients::findFirstBy_($urlrequest) : $c = Companies::findFirstBy_($urlrequest);

      $user = Users::findFirstByEmail($c->email);

      $form = new Form();

        $form->add(new Hidden( "security" ,[
            'name'  => $this->security->getTokenKey(),
            'value' => $this->security->getToken()
        ]));

        $form->add(new Hidden( "user_id" ,[
            'value' => $user->_
        ]));

        $form->add(new Text( "firstName" ,[
            'class'         => "form-control",
            'id'            => "firstName",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $c->firstname
        ]));

        $form->add(new Text( "lastName" ,[
            'class'         => "form-control",
            'id'            => "lastName",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $c->lastname
        ]));

        $form->add(new Text( "document" ,[
            'class'         => "form-control",
            'id'            => "document",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $c->document
        ]));

        $form->add(new Text( "email" ,[
            'class'         => "form-control",
            'id'            => "email",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'data-email'    => "* E-Mail Inválido",
            'value'         => $c->email
        ]));

        $form->add(new Text( "phone" ,[
            'class'         => "form-control",
            'id'            => "phone",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'data-inputmask'=> "'alias' : 'phone'",
            'value'         => $c->phone
        ]));

        $form->add(new Text( "domain" ,[
            'class'         => "form-control",
            'id'            => "domain",
            'value'         => $c->domain
        ]));

        $form->add(new Text( "address" ,[
            'class'         => "form-control",
            'id'            => "address",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $c->address
        ]));

        $form->add(new Text( "district" ,[
            'class'         => "form-control",
            'id'            => "district",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $c->district
        ]));

        $form->add(new Text( "zip" ,[
            'class'         => "form-control",
            'id'            => "zip",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $c->zip
        ]));

        $form->add(new Text( "city" ,[
            'class'         => "form-control",
            'id'            => "city",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $c->city
        ]));

        $form->add(new Text( "state" ,[
            'class'         => "form-control",
            'id'            => "state",
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $c->state
        ]));

        $form->add(new Text( "username" ,[
            'class'         => "form-control",
            'id'            => "username",
            'value'         => $user->username
        ]));

        $form->add(new Password( "password" ,[
            'class'         => "form-control",
            'id'            => "password",
        ]));

        if( $type === 'company')
        {

          $form->add(new Text( "registration" ,[
              'class'         => "form-control",
              'id'            => "registration",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
              'value'         => $c->registration
          ]));

          $form->add(new Text( "company" ,[
              'class'         => "form-control",
              'id'            => "company",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
              'value'         => $c->company
          ]));

          $form->add(new Text( "fantasy" ,[
              'class'         => "form-control",
              'id'            => "fantasy",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
              'value'         => $c->fantasy
          ]));

          $form->add(new Text( "role" ,[
              'class'         => "form-control",
              'id'            => "role",
              'data-validate' => true,
              'data-empty'    => "* Campo Obrigatório",
              'value'         => $c->role
          ]));

          $form->add(new Text( "contact_name[]" ,[
              'class'         => "form-control",
          ]));

          $form->add(new Text( "contact_phone[]" ,[
              'class'         => "form-control",
          ]));

          $form->add(new Text( "contact_area[]" ,[
              'class'         => "form-control",
          ]));

        }

      switch ($type) {
        case 'person':  $size = 6 ; break;
        case 'company': $size = 3 ; break;
      }

      $this->view->form = $form;
      $this->view->type = $type;
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

        if( $this->dispatcher->getParam("type") === "person" )
        {
          $person = new Clients;
            $person->firstname = $this->request->getPost("firstName");
            $person->lastname  = $this->request->getPost("lastName");
            $person->document  = $this->request->getPost("document");
            $person->email     = $this->request->getPost("email");
            $person->phone     = $this->request->getPost("phone");
            $person->domain    = $this->request->getPost("domain");
            $person->address   = $this->request->getPost("address");
            $person->district  = $this->request->getPost("district");
            $person->zip       = $this->request->getPost("zip");
            $person->city      = $this->request->getPost("city");
            $person->state     = $this->request->getPost("state");
          $person->save();
        }
        else if( $this->dispatcher->getParam("type") === "company" )
        {
          $company = new Companies;
            $company->firstname = $this->request->getPost("firstName");
            $company->lastname  = $this->request->getPost("lastName");
            $company->company   = $this->request->getPost("company");
            $company->fantasy   = $this->request->getPost("fantasy");
            $company->document  = $this->request->getPost("document");
            $company->registration  = $this->request->getPost("registration");
            $company->role      = $this->request->getPost("role");
            $company->email     = $this->request->getPost("email");
            $company->phone     = $this->request->getPost("phone");
            $company->domain    = $this->request->getPost("domain");
            $company->address   = $this->request->getPost("address");
            $company->district  = $this->request->getPost("district");
            $company->zip       = $this->request->getPost("zip");
            $company->city      = $this->request->getPost("city");
            $company->state     = $this->request->getPost("state");
          $company->save();

          $m = array_combine(
            ['name','phone','area'],
            [$this->request->getPost('contact_name'),$this->request->getPost('contact_phone'),$this->request->getPost('contact_area')]
          );

          for($i=0; $i < count($m['name']); $i++)
          {
            $contact = new ClientContacts;
              $contact->client = $company->_;
              $contact->name  = $m['name'][$i];
              $contact->phone = $m['phone'][$i];
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

      $u = Users::findFirst($this->request->getPost("user_id"));

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

        if( $this->dispatcher->getParam("type") === "person" )
        {
          $person = Clients::findFirstBy_($this->dispatcher->getParam("urlrequest"));
            $person->firstname = $this->request->getPost("firstName");
            $person->lastname  = $this->request->getPost("lastName");
            $person->document  = $this->request->getPost("document");
            $person->email     = $this->request->getPost("email");
            $person->phone     = $this->request->getPost("phone");
            $person->domain    = $this->request->getPost("domain");
            $person->address   = $this->request->getPost("address");
            $person->district  = $this->request->getPost("district");
            $person->zip       = $this->request->getPost("zip");
            $person->city      = $this->request->getPost("city");
            $person->state     = $this->request->getPost("state");
          $person->save();
        }
        else if( $this->dispatcher->getParam("type") === "company" )
        {
          $company = Companies::findFirstBy_($this->dispatcher->getParam("urlrequest"));
            $company->firstname = $this->request->getPost("firstName");
            $company->lastname  = $this->request->getPost("lastName");
            $company->company   = $this->request->getPost("company");
            $company->fantasy   = $this->request->getPost("fantasy");
            $company->document  = $this->request->getPost("document");
            $company->registration  = $this->request->getPost("registration");
            $company->role      = $this->request->getPost("role");
            $company->email     = $this->request->getPost("email");
            $company->phone     = $this->request->getPost("phone");
            $company->domain    = $this->request->getPost("domain");
            $company->address   = $this->request->getPost("address");
            $company->district  = $this->request->getPost("district");
            $company->zip       = $this->request->getPost("zip");
            $company->city      = $this->request->getPost("city");
            $company->state     = $this->request->getPost("state");
          $company->save();

          foreach( ClientContacts::findByClient($this->dispatcher->getParam("urlrequest")) as $contact ): $contact->delete(); endforeach;

          $m = array_combine(
            ['name','phone','area'],
            [$this->request->getPost('contact_name'),$this->request->getPost('contact_phone'),$this->request->getPost('contact_area')]
          );

          for($i=0; $i < count($m['name']); $i++)
          {
            $contact = new ClientContacts;
              $contact->client = $company->_;
              $contact->name  = $m['name'][$i];
              $contact->phone = $m['phone'][$i];
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

        if( $this->dispatcher->getParam("type") === "person" )
        {
          $c = Clients::findFirst($this->dispatcher->getParam("urlrequest"));
          $u = Users::findFirstByEmail($c->email);
          $n = "{$c->firstname} {$c->lastname}";
            $c->delete();
            $u->delete();

          $flags['title']     = "Removido Com Sucesso!";
          $flags['text']      = "Cliente Removido com Sucesso.";
          $flags['redirect']  = "/clients";
          $flags['time']      = 3200;
        }
        else if( $this->dispatcher->getParam("type") === "company" )
        {
          $c = Companies::findFirst($this->dispatcher->getParam("urlrequest"));
          $u = Users::findFirstByEmail($c->email);
          $n = "{$c->firstname} {$c->lastname}";
            $c->delete();
            $u->delete();
          foreach( ClientContacts::findByClient($this->dispatcher->getParam("urlrequest")) as $client )
          {
            $client->delete();
          }
          $flags['title']     = "Removido Com Sucesso!";
          $flags['text']      = "Cliente Removido com Sucesso.";
          $flags['redirect']  = "/clients";
          $flags['time']      = 3200;
        }

        # Log What Happend
        $this->logManager($this->logs->delete,"Removeu um cliente ({$n}).");

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
