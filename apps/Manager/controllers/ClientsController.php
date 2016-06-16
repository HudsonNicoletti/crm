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

      $urlrequest = $this->dispatcher->getParam('id');

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

    public function PersonAction()
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

      if(!$this->isEmail($this->request->getPost("email"))):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Endereço de E-Mail inválido!";
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

        if($this->request->hasFiles()):
          foreach($this->request->getUploadedFiles() as $file):
            $filename = substr(sha1(uniqid()), 0, 12).'.'.$file->getExtension();
            $file->moveTo("assets/manager/images/avtar/{$filename}");
          endforeach;
        else:
          $filename = 'avtar.jpg';
        endif;

        $user = new Users;
          $user->username   = $this->request->getPost("username","string");
          $user->password   = password_hash($this->request->getPost("password","string"), PASSWORD_BCRYPT );
          $user->email      = $this->request->getPost("email","email");
          $user->permission = $this->permissions->client;
        $user->save();

        $client = new Clients;
          $client->user      = $user->_;
          $client->firstname = $this->request->getPost("firstName","string");
          $client->lastname  = $this->request->getPost("lastName","string");
          $client->document  = $this->request->getPost("document","string");
          $client->phone     = $this->request->getPost("phone","string");
          $client->cellphone = $this->request->getPost("cellphone","string");
          $client->domain    = $this->request->getPost("domain","string");
          $client->address   = $this->request->getPost("address","string");
          $client->district  = $this->request->getPost("district","string");
          $client->zip       = $this->request->getPost("zip","string");
          $client->city      = $this->request->getPost("city","string");
          $client->state     = $this->request->getPost("state","string");
          $client->image     = $filename;
        $client->save();

        $name = $this->request->getPost("firstName")." ".$this->request->getPost("lastName");
        # Log What Happend
        $this->logManager($this->logs->create,"Cadastrou um novo cliente ( {$name} ).");

        $this->flags['title']      = "Cadastrado com Sucesso!!";
        $this->flags['text']       = "Cliente Cadastrado com sucesso!";
        $this->flags['redirect']   = "/clients";
        $this->flags['time']       = 1200;

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

    public function CompanyAction()
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

      if(!$this->isEmail($this->request->getPost("email"))):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Cadastrar!";
        $this->flags['text']   = "Endereço de E-Mail inválido!";
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

        if($this->request->hasFiles()):
          foreach($this->request->getUploadedFiles() as $file):
            $filename = substr(sha1(uniqid()), 0, 12).'.'.$file->getExtension();
            $file->moveTo("assets/manager/images/avtar/{$filename}");
          endforeach;
        else:
          $filename = 'avtar.jpg';
        endif;

        $user = new Users;
          $user->username   = $this->request->getPost("username","string");
          $user->password   = password_hash($this->request->getPost("password","string"), PASSWORD_BCRYPT );
          $user->email      = $this->request->getPost("email","email");
          $user->permission = $this->permissions->client;
        $user->save();

        $client = new Clients;
          $client->user      = $user->_;
          $client->firstname = $this->request->getPost("firstName","string");
          $client->lastname  = $this->request->getPost("lastName","string");
          $client->document  = $this->request->getPost("document","string");
          $client->phone     = $this->request->getPost("phone","string");
          $client->cellphone = $this->request->getPost("cellphone","string");
          $client->domain    = $this->request->getPost("domain","string");
          $client->address   = $this->request->getPost("address","string");
          $client->district  = $this->request->getPost("district","string");
          $client->zip       = $this->request->getPost("zip","string");
          $client->city      = $this->request->getPost("city","string");
          $client->state     = $this->request->getPost("state","string");
          $client->image     = $filename;
        $client->save();

        $company = new Companies;
          $company->client    = $client->_;
          $company->company   = $this->request->getPost("company","string");
          $company->fantasy   = $this->request->getPost("fantasy","string");
          $company->registration  = $this->request->getPost("registration","string");
          $company->role      = $this->request->getPost("role","string");
        $company->save();

        $m = array_combine(
          ['name','phone','cellphone','email','area'],
          [
            $this->request->getPost('contact_name',"string"),
            $this->request->getPost('contact_phone',"string"),
            $this->request->getPost('contact_cellphone',"string"),
            $this->request->getPost('contact_email',"email"),
            $this->request->getPost('contact_area',"string")
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

        $name = $this->request->getPost("fantasy");
        # Log What Happend
        $this->logManager($this->logs->create,"Cadastrou um novo cliente ( {$name} ).");

        $this->flags['title']      = "Cadastrado com Sucesso!!";
        $this->flags['text']       = "Cliente Cadastrado com sucesso!";
        $this->flags['redirect']   = "/clients";
        $this->flags['time']       = 1200;

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
        $this->flags['title']  = "Erro ao Remover!";
        $this->flags['text']   = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Remover!";
        $this->flags['text']   = "Token de segurança inválido.";
      endif;

      if(!$this->dispatcher->getParam("id")):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Remover!";
        $this->flags['text']   = "Cliente não especificado.";
      endif;

      if($this->flags['status']):

        $c = Clients::findFirst($this->dispatcher->getParam("id"));
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

    public function ModalAction()
    {
      $this->response->setContentType("application/json");

      if(!$this->request->isGet()):
        $this->flags['status'] = false ;
        $this->flags['title']  = "Erro ao Alterar!";
        $this->flags['text']   = "Metodo Inválido.";
      endif;

      if($this->flags['status']):

        $form = new Form();
        $action = false;
        $inputs = [];

        $id = $this->dispatcher->getParam('id');

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
        ->where("Manager\Models\Clients._ = '{$id}'")
        ->leftJoin('Manager\Models\Companies', 'Manager\Models\Companies.client = Manager\Models\Clients._')
        ->innerJoin('Manager\Models\Users', 'Manager\Models\Users._ = Manager\Models\Clients.user')
        ->execute();

        $element['firstName'] = new Text( "firstName" ,[
          'class'         => "form-control",
          'title'         => "Nome",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório"
        ]);

        $element['lastName'] = new Text( "lastName" ,[
          'class'         => "form-control",
          'title'         => "SobreNome"
        ]);

        $element['document'] = new Text( "document" ,[
          'class'         => "form-control",
          'title'         => "CPF | CNPJ"
        ]);

        $element['email'] = new Text( "email" ,[
          'class'         => "form-control",
          'title'         => "E-Mail",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
          'data-email'    => "* E-Mail Inválido"
        ]);

        $element['phone'] = new Text( "phone" ,[
          'class'         => "form-control",
          'title'         => "Telefone"
        ]);

        $element['cellphone'] = new Text( "cellphone" ,[
          'class'         => "form-control",
          'title'         => "Celular"
        ]);

        $element['domain'] = new Text( "domain" ,[
          'class'         => "form-control",
          'title'         => "Domínio"
        ]);

        $element['address'] = new Text( "address" ,[
          'class'         => "form-control",
          'title'         => "Endereço",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
        ]);

        $element['district'] = new Text( "district" ,[
          'class'         => "form-control",
          'title'         => "Bairro",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
        ]);

        $element['zip'] = new Text( "zip" ,[
          'class'         => "form-control",
          'title'         => "CEP",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
        ]);

        $element['city'] = new Text( "city" ,[
          'class'         => "form-control",
          'title'         => "Cidade",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
        ]);

        $element['state'] = new Text( "state" ,[
          'class'         => "form-control",
          'title'         => "Estado (UF)",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
        ]);

        $element['file'] = new File( "file" ,[
          'class'         => "form-control",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
        ]);

        $element['username'] = new Text( "username" ,[
          'class'         => "form-control",
          'title'         => "Usuário",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
        ]);

        $element['password'] = new Password( "password" ,[
          'class'         => "form-control",
          'title'         => "Senha",
          'data-validate' => true,
          'data-empty'    => "* Campo Obrigatório",
        ]);

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

          }

        # CREATING ELEMENTS

        $element['security'] = new Hidden( "security" ,[
            'name'  => $this->security->getTokenKey(),
            'value' => $this->security->getToken(),
        ]);

        # IF REQUEST IS TO CREATE JUST POPULATE WITH DEFAULT ELEMENTS
        if( $this->dispatcher->getParam("method") == "create" ):
          $action = "/tasks/new";
          $template = "create";
          foreach($element as $e)
          {
            $form->add($e);
          }

        # IF REQUEST IS TO UPDATE POPULATE WITH VALJUE TO ELEMENT
        elseif ($this->dispatcher->getParam("method") == "update"):
          $task = Tasks::findFirst($this->dispatcher->getParam("task"));
          $action = "/tasks/update/{$task->_}";
          $template = "update";
          $element['project']->setAttribute("value",$task->project);
          $element['title']->setAttribute("value",$task->title);
          $element['description']->setAttribute("value",$task->description);
          $element['deadline']->setAttribute("value",(new \DateTime($task->deadline))->format("d-m-Y"));
          $element['assigned']->setAttribute("value",$task->assigned);
          foreach($element as $e)
          {
            $form->add($e);
          }

        # IF REQUEST IS TO VIEW POPULATE WITH VALJUE TO ELEMENT
        elseif ($this->dispatcher->getParam("method") == "view"):
          $template = "view";
          $task = Tasks::findFirst($this->dispatcher->getParam("task"));
          $element['project']->setAttribute("disabled",true)->setAttribute("value",$task->project);
          $element['title']->setAttribute("disabled",true)->setAttribute("value",$task->title);
          $element['description']->setAttribute("disabled",true)->setAttribute("value",$task->description);
          $element['deadline']->setAttribute("disabled",true)->setAttribute("value",(new \DateTime($task->deadline))->format("d-m-Y"));
          $element['assigned']->setAttribute("disabled",true)->setAttribute("value",$task->assigned);
          foreach($element as $e)
          {
            $form->add($e);
          }

        endif;

        # POPULATE ARRAY WITH TITLE AND INPUTS FOR RENDERING
        foreach($form as $f)
        {
          array_push($inputs,[ "title" => $f->getAttribute("title") , "input" => $f->render($f->getName()) ]);
        }

        # RENDER
        $body = (new Mustache)->render(file_get_contents($_SERVER['DOCUMENT_ROOT']."/templates/modal.tpl"),[
          $template => true,
          "action"  => $action,
          "inputs"  => $inputs
        ]);

      endif;

      return $this->response->setJsonContent([
        "status"    =>  $this->flags['status'],
        "data"      =>  [ "#{$template}" , $body ]  # Modal Target , data
      ]);

      $this->response->send();
      $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}
