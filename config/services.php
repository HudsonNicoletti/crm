<?php

use Phalcon\Mvc\Dispatcher\Exception as DispatchException,
    Phalcon\Session\Adapter\Files as SessionAdapter,
    Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
    Phalcon\Mvc\Dispatcher as PhDispatcher,
    Phalcon\Events\Manager as EventsManager,
    Phalcon\Mvc\Url as UrlResolver,
    Phalcon\Config\Adapter\Ini,
    Phalcon\DI\FactoryDefault,
    Phalcon\Mvc\Router,
    Phalcon\Dispatcher;

$di = new FactoryDefault();
$cf = new Ini("../config/config.ini");

#   Database connection
$di['db'] = function() use ($cf) {
    return new DbAdapter([
        "host"      => $cf->database->host,
        "username"  => $cf->database->username,
        "password"  => $cf->database->password,
        "dbname"    => $cf->database->dbname,
        "charset"   => $cf->database->charset
    ]);
};

#    The URL component is used to generate all kinds of URLs in the application
$di['url'] = function () {
    $url = new UrlResolver();
    $url->setBaseUri('/');

    return $url;
};

#   Starts the session the first time some component requests the session service
$di['session'] = function (){
    $session = new SessionAdapter();
    $session->start();

    return $session;
};

#   Handles 404
$di['dispatcher'] = function () {
    $eventsManager = new EventsManager();
    $eventsManager->attach("dispatch:beforeException", function($event, $dispatcher, $exception) {

        if ($exception instanceof DispatchException) {
            $dispatcher->forward(array(
                'controller' => 'Error',
                'action'     => 'NotFound'
            ));
            return false;
        }

    });

    $dispatcher = new PhDispatcher();
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
};

#   Configure PHPMailer ( loaded by composer ) , returning the mail ini config & PHPMailer functions
$di['mail'] = function () use ($cf) {
    $mail = new PHPMailer;

    $mail->isSMTP();
    $mail->isHTML(true);

    $mail->CharSet      = $cf->mail->charset;
    $mail->Host         = $cf->mail->host;
    $mail->SMTPAuth     = true;
    $mail->Username     = $cf->mail->username;
    $mail->Password     = $cf->mail->password;
    $mail->SMTPSecure   = $cf->mail->security;
    $mail->Port         = $cf->mail->port;

    #   Pass as object only MAIL config and PHPMailer Functions.
    return (object)[
      "name"      => $cf->mail->name ,
      "email"     => $cf->mail->email ,
      "functions" => $mail
    ];
};

#   Configure system login permissions
$di['permissions'] = function () use ($cf) {
    return $cf->permissions;
};

#   Configure system Logs
$di['logs'] = function () use ($cf) {
    return $cf->logs;
};

#    Registering a router
$di['router'] = function () {
    $router = new Router();

    $router->setDefaultModule('Manager');
    $router->setDefaultNamespace('Manager\Controllers');
    $router->removeExtraSlashes(true);

    require( "routes.php" );

    return $router;
};
