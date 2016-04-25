<?php

use Phalcon\Mvc\Application;

try {

    #   Include Composer
    require __DIR__ . '/../libraries/autoload.php';

    #   Include services
    require __DIR__ . '/../config/services.php';

    #   Handle the request
    $application = new Application($di);

    #   Include modules
    require __DIR__ . '/../config/modules.php';

    echo $application->handle()->getContent();

}
catch (Exception $e) {

    if( $cf->debug->active )
    {
        echo $e->getMessage();
    }
    else
    {
        header("Location: /error/ServerError");
    }

}
