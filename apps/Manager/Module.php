<?php

namespace Manager;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Config\Adapter\Ini;

class Module
{

    public function registerAutoloaders()
    {
        $loader = new Loader();

        $loader->registerNamespaces([
            'Manager\Controllers'  => __DIR__ . '/controllers/',
            'Manager\Models'       => __DIR__ . '/models/'
        ]);

        $loader->register();
    }

    public function registerServices($di)
    {

        $config = new Ini("../config/config.ini");

        $di['view'] = function() {
            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');

            return $view;
        };

    }

}
