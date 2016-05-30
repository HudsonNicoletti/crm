<?php

namespace Manager\Controllers;

use \Manager\Models\Users as Users;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Hidden;

class LoginController extends ControllerBase
{
    private $flags = [
      "status"    =>  true,
      "title"     =>  null,
      "text"      =>  null,
      "redirect"  =>  false,
      "time"      =>  null,
    ];

    public function IndexAction()
    {
      $this->assets
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator.min.js")
      ->addJs("assets/manager/js/plugins/bootstrap-validator/bootstrapValidator-conf.js");

      $form = new Form();

      $form->add(new Hidden( "security" ,[
        'name'  => $this->security->getTokenKey(),
        'value' => $this->security->getToken()
      ]));

      $form->add(new Text( "username" ,[
        'placeholder'  => "Usuário | E-Mail",
        'class'        => "form-control",
        'id'           => "username",
      ]));

      $form->add(new Password( "password" ,[
        'placeholder'  => "Senha",
        'class'        => "form-control",
        'id'           => "password",
      ]));

      $this->view->form = $form;
    }

    public function AuthAction()
    {

      $this->response->setContentType("application/json");

      if(!$this->request->isPost()):
        $this->flags['status']    = false ;
        $this->flags['title']     = "Erro ao Cadastrar!";
        $this->flags['text']      = "Metodo Inválido.";
      endif;

      if(!$this->security->checkToken()):
        $this->flags['status']    = false ;
        $this->flags['title']     = "Erro ao Cadastrar!";
        $this->flags['text']      = "Token de segurança inválido.";
      endif;

      if( $this->flags['status'] )
      {

        $username = preg_replace('/\s+/', '', $this->request->getPost("username","string"));
        $password = preg_replace('/\s+/', '', $this->request->getPost("password","string"));

        ( $this->isEmail( $username ) == false ) ? $user = Users::findFirstByUsername($username) : $user = Users::findFirstByEmail($username);

        #   check if user permission level is between team & dev values
        if($user->_ != NULL && in_array( $user->permission , range( $this->permissions->client, $this->permissions->dev )) === true )
        {
          if(password_verify( $password , $user->password ) == true)
          {
              $this->session->set("secure_id", $user->_);

              $this->flags['title']     = "Bem Vindo(a)!";
              $this->flags['text']      = "";
              $this->flags['redirect']  = "/";
              $this->flags['time']      = 800;
          }
          else
          {
            $this->flags['title']     = "Erro ao Acessar Painel!";
            $this->flags['text']      = "Senha Fornecida Inválida !";
          }
        }
        else
        {
          $this->flags['status']    = false;
          $this->flags['title']     = "Erro ao Acessar Painel!";
          $this->flags['text']      = "Você não tem permissão para acessar esta área ou seu nome de usuário está incorreto!";
        }
      }

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

    public function LogoutAction()
    {
      $this->session->destroy();
      return $this->response->redirect();
    }


}
