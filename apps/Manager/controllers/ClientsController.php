<?php

namespace Manager\Controllers;

use Manager\Models\Users as Users,
    Manager\Models\Clients as Clients,
    Manager\Models\Companies as Companies,
    Manager\Models\ClientContacts as ClientContacts;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\File,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Hidden;

class ClientsController extends ControllerBase
{
    private  $flags = [
      'status'    => true,
      'title'     => false,
      'text'      => false,
      'redirect'  => false,
      'time'      => null,
      'target'    => false
    ];

    public function IndexAction()
    {
      $this->assets
      ->addCss("assets/manager/css/app/email.css")
      ->addJs("assets/manager/js/plugins/jquery.filtr.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

      $clients = Clients::query()
      ->columns([
        'Manager\Models\Clients._',
        'Manager\Models\Clients.firstname',
        'Manager\Models\Clients.lastname',
        'Manager\Models\Clients.document',
        'Manager\Models\Clients.phone',
        'Manager\Models\Clients.domain',
        'Manager\Models\Clients.image',
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
      $this->assets
      ->addCss("assets/manager/css/app/email.css")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

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

      $form->add(new File( "file" ,[
        'class'         => "form-control",
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
      $this->assets
      ->addCss("assets/manager/css/app/email.css")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

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
            'data-validate' => true,
            'data-empty'    => "* Campo Obrigatório",
            'value'         => $client[0]->state
        ]));

        $form->add(new File( "file" ,[
          'class'         => "form-control",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
        ]));

        $form->add(new Text( "username" ,[
            'class'         => "form-control",
            'value'         => $client[0]->username
        ]));

        $form->add(new Password( "password" ,[
            'class'         => "form-control",
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
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Token de segurança inválido.";
      endif;

      if(!$this->isEmail($this->request->getPost("email"))):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Endereço de E-Mail inválido!";
      endif;

      if(!$this->request->hasFiles()):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Nehuma Imagem Selecionada!";
      endif;

      if( Users::findFirstByEmail($this->request->getPost("email"))->_ != NULL ):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Endereço de E-Mail já cadastrado!";
      endif;

      if( Users::findFirstByUsername($this->request->getPost("username"))->_ != NULL ):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Nome de usuário já cadastrado!";
      endif;

      if($this->flags['status']):

        foreach($this->request->getUploadedFiles() as $file)
        {
          $filename = substr(sha1(uniqid()), 0, 12).'.'.$file->getExtension();
          $file->moveTo("assets/manager/images/avtar/{$filename}");
        }

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
          $client->image     = $filename;
        $client->save();

        if( $this->dispatcher->getParam("type") == "company" )
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

        $this->flags['status'] = true ;
        $this->flags['title']  = "Cadastrado com Sucesso!!";
        $this->flags['text']   = "Cliente Cadastrado com sucesso!";
        $this->flags['redirect']   = "/clients";
        $this->flags['time']   = 1200;

      endif;

      return $this->response->setJsonContent([
        "status" => $this->flags['status'] ,
        "title"  => $this->flags['title'] ,
        "text"   => $this->flags['text'],
        "redirect"   => $this->flags['redirect'],
        "time"   => $this->flags['time'],
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function UpdateAction()
    {
      $this->response->setContentType("application/json");

      $c = Clients::findFirst($this->dispatcher->getParam("urlrequest"));
      $company = Companies::findFirstByClient($c->_);
      $u = Users::findFirst($c->user);

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

      if(!$this->isEmail($this->request->getPost("email"))):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Endereço de E-Mail inválido!";
      endif;

      if( $this->request->getPost("email") != $u->email && Users::findFirstByEmail($this->request->getPost("email"))->_ != NULL ):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Endereço de E-Mail já cadastrado!";
      endif;

      if( $this->request->getPost("username") != $u->username && Users::findFirstByusername($this->request->getPost("username"))->_ != NULL ):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Nome de usuário já cadastrado!";
      endif;

      if($this->flags['status']):

        # Handle image
        if($this->request->hasFiles()):
          unlink("assets/manager/images/avtar/{$c->image}");
          foreach($this->request->getUploadedFiles() as $file):
            $filename = substr(sha1(uniqid()), 0, 12).'.'.$file->getExtension();
            $file->moveTo("assets/manager/images/avtar/{$filename}");
          endforeach;
        else:
          $filename = $c->image;
        endif;

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
          $c->image     = $filename;
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

        $this->flags['status'] = true ;
        $this->flags['title']  = "Alterado com Sucesso!!";
        $this->flags['text']   = "Cliente Alterado com sucesso!";
        $this->flags['redirect']   = "/clients";
        $this->flags['time']   = 1200;

      endif;

      return $this->response->setJsonContent([
        "status" => $this->flags['status'] ,
        "title"  => $this->flags['title'] ,
        "text"   => $this->flags['text'],
        "redirect"   => $this->flags['redirect'],
        "time"   => $this->flags['time'],
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }

    public function RemoveAction()
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

        unlink("assets/manager/images/avtar/{$c->image}");

        # Log What Happend
        $this->logManager($this->logs->delete,"Removeu um cliente ({$n}).");

        $this->flags['title']     = "Removido Com Sucesso!";
        $this->flags['text']      = "Cliente Removido com Sucesso.";
        $this->flags['redirect']  = "/clients";
        $this->flags['time']      = 1000;

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
