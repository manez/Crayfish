<?php

require_once __DIR__.'/../vendor/autoload.php';

use Islandora\Crayfish\Commons\Provider\IslandoraServiceProvider;
use Islandora\Crayfish\Commons\Provider\YamlConfigServiceProvider;
use Islandora\Houdini\Controller\HoudiniController;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

$app->register(new IslandoraServiceProvider());
$app->register(new YamlConfigServiceProvider(__DIR__ . '/../cfg/config.yaml'));

$app['houdini.controller'] = function ($app) {
    return new HoudiniController(
        $app['crayfish.cmd_execute_service'],
        $app['crayfish.houdini.formats.valid'],
        $app['crayfish.houdini.formats.default'],
        $app['crayfish.houdini.executable'],
        $app['monolog']
    );
};

$app->options('/convert', "houdini.controller:convertOptions");
$app->get('/convert', "houdini.controller:convert")
    ->before(function (Request $request, Application $app) {
        return $app['crayfish.apix_middleware']->before($request);
    });

$app->options('/identify', "houdini.controller:identifyOptions");
$app->get('/identify', "houdini.controller:identify")
    ->before(function (Request $request, Application $app) {
        return $app['crayfish.apix_middleware']->before($request);
    });

return $app;
