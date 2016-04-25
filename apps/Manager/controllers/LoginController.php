<?php

namespace Manager\Controllers;

use \Manager\Models\Users as Users;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Hidden;

class LoginController extends ControllerBase
{
    public function IndexAction()
    {

      if ($this->session->has("secure_id")): return $this->response->redirect("index"); endif;

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

        if( $this->request->isPost() )
        {
            if ($this->security->checkToken()):
                $this->response->setStatusCode(200,"OK");

                $username = preg_replace('/\s+/', '', $this->request->getPost("username","string"));
                $password = preg_replace('/\s+/', '', $this->request->getPost("password","string"));

                ( $this->isEmail( $username ) == false ) ? $user = Users::findFirstByUsername($username) : $user = Users::findFirstByEmail($username);

                #   check if user permission level is between team & dev values
                if($user->_ != NULL && in_array( $user->permission , range( $this->permissions->client, $this->permissions->dev )) === true )
                {
                    if(password_verify( $password , $user->password ) == true){

                        $this->session->set("secure_id", $user->_);
                        return $this->response->setJsonContent([
                            "status"    => true,
                            "redirect"  => '/',
                            "time"      => 0
                        ]);

                    }
                    else{

                        return $this->response->setJsonContent([
                            "status"  =>    false ,
                            "message" =>    "Unauthorized, Invalid passowrd.",
                            "title"   =>    "Erro ao Acessar Painel!",
                            "text"    =>    "Senha Fornecida Inválida !"
                        ]);

                    }
                }
                else
                {
                    return $this->response->setJsonContent([
                        "status"  =>    false ,
                        "message" =>    "",
                        "title"   =>    "Erro ao Acessar Painel!",
                        "text"    =>    "Você não tem permissão para acessar esta área ou seu nome de usuário está incorreto!"
                    ]);
                }

            else:
                $this->response->setStatusCode(401,"Unauthorized");
                 return $this->response->setJsonContent([
                    "status"  =>    false ,
                    "message" =>    "Unauthorized , CSRF Token or Key invalid.",
                    "title"   =>    "Erro ao remover a postagem!",
                    "text"    =>    "Ocorreu um erro durante a operação."
                ]);
            endif;
        }
        else
        {
            $this->response->setStatusCode(403,"Forbidden");
            return $this->response->setJsonContent([
                "status"  =>    false ,
                "message" =>    "Request method must be valid."
            ]);
        }

        $this->response->send();
        $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);

    }

    public function LogoutAction()
    {

        $this->session->destroy();
        return $this->response->redirect();

    }


}
