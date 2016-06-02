<!-- TICKETS -->
{{#TICKET_CREATE}}
<div style="background-color: #eeeeef; padding: 50px 0; ">
  <div style="max-width:640px; margin:0 auto; font-family: Helvetica, arial, sans-serif;">
    <!-- <img src='http://agenciadzoe.com.br/assets/public/images/logo-alt.png' alt='' border='0' style='display: inline-block; outline: none; border: none; height: 80px; margin:15px'> -->
    <div style="color: #fff; text-align: center; background-color:#2B2F3E; padding: 30px; border-top-left-radius: 3px; border-top-right-radius: 3px; margin: 0;">
      <h1>Chamado #{{ticket_id}} Aberto</h1>
    </div>
    <div style="padding: 20px; background-color: rgb(255, 255, 255);">
      <p>Um novo chamado foi aberto.</p>

      <p><strong>Chamado:</strong> {{ticket}} , em {{created}} &mdash; #{{ticket-id}}</p>

      <p><strong>Cliente:</strong> {{client}}</p>

      <p><a href="{{dashboard}}">Acessar Painel</a></p>
    </div>
  </div>
</div>
{{/TICKET_CREATE}}

{{#TICKET_RESPONSE}}
<div style="background-color: #eeeeef; padding: 50px 0; ">
  <div style="max-width:640px; margin:0 auto; font-family: Helvetica, arial, sans-serif;">
    <!-- <img src='http://agenciadzoe.com.br/assets/public/images/logo-alt.png' alt='' border='0' style='display: inline-block; outline: none; border: none; height: 80px; margin:15px'> -->
    <div style="color: #fff; text-align: center; background-color:#2B2F3E; padding: 30px; border-top-left-radius: 3px; border-top-right-radius: 3px; margin: 0;">
      <h1>Chamado #{{ticket_id}}</h1>
    </div>
    <div style="padding: 20px; background-color: rgb(255, 255, 255);">
      <p>Olá {{NAME}},</p>

      <p>Seu chamado <strong>{{ticket}}</strong> recebeu uma nova resposta.</p>

      <p>Acesse o painel agora e veja sua resposta.</p>

      <p><a href="{{dashboard}}">Acessar</a></p>
    </div>
  </div>
</div>
{{/TICKET_RESPONSE}}

<!-- ACCOUNTS -->
{{#ACCOUNT_CREATE}}
<div style="background-color: #eeeeef; padding: 50px 0; ">
  <div style="max-width:640px; margin:0 auto; font-family: Helvetica, arial, sans-serif;">
    <!-- <img src='http://agenciadzoe.com.br/assets/public/images/logo-alt.png' alt='' border='0' style='display: inline-block; outline: none; border: none; height: 80px; margin:15px'> -->
    <div style="color: #fff; text-align: center; background-color:#2B2F3E; padding: 30px; border-top-left-radius: 3px; border-top-right-radius: 3px; margin: 0;">
      <h1>Acesso ao Painel</h1>
    </div>
    <div style="padding: 20px; background-color: rgb(255, 255, 255);">
      <p>Olá {{NAME}},</p>

      <p>Uma conta foi criada para você.</p>

      <p>Utilize a seguinte informação para acessar seu painel de controle:</p>

      <P>URL Painel: {{DASHBOARD_URL}} </P>

      <P>Usuário: {{USERNAME}} ou Email: {{EMAIL}} </P>

      <P>Senha: {{PASSWORD}}</P>
    </div>
  </div>
</div>
{{/ACCOUNT_CREATE}}

{{#ACCOUNT_RESET}}
<div style="background-color: #eeeeef; padding: 50px 0; ">
  <div style="max-width:640px; margin:0 auto; font-family: Helvetica, arial, sans-serif;">
    <!-- <img src='http://agenciadzoe.com.br/assets/public/images/logo-alt.png' alt='' border='0' style='display: inline-block; outline: none; border: none; height: 80px; margin:15px'> -->
    <div style="color: #fff; text-align: center; background-color:#2B2F3E; padding: 30px; border-top-left-radius: 3px; border-top-right-radius: 3px; margin: 0;">
      <h1>Redefinição de Senha</h1>
    </div>
    <div style="padding: 20px; background-color: rgb(255, 255, 255);">
      <p>Olá {{NAME}},</p>

      <p>Um pedido de redefinição de senha foi criado para a sua conta.</p>

      <p>Para iniciar o processo de redefinição de senha, clique no link a seguir:</p>

      <p><a href="#">Alterar Senha</a></p>

      <p>Se você recebeu esta mensagem por engano, é provável que outro usuário tenha inserido seu endereço de e-mail por engano ao tentar redefinir uma senha.</p>

      <p>Se você não iniciou a solicitação, não precisa tomar nenhuma ação e pode desconsiderar este e-mail.</p>
    </div>
  </div>
</div>
{{/ACCOUNT_RESET}}
