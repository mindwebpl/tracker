<?php

require_once __DIR__.'/../vendor/autoload.php';

use Mindweb\Log;
use Mindweb\Config;
use Mindweb\Db;
use Mindweb\Tracker\Controller\TrackController;

$app = new Silex\Application();
$app->register(new Silex\Provider\ServiceControllerServiceProvider());

$app['configuration'] = $app->share(function () {
    return Config\ConfigurationFactory::factory('Mindweb', 'ConfigJson', '../config.json');
});

$app['tracker.connection'] = $app->share(function () use ($app) {
    return Db\ConnectionFactory::create(
        $app['configuration'],
        'tracker.db.type',
        'tracker.db.adapter'
    );
});

$app['log.repository'] = $app->share(function () use ($app) {
    $logRepositoryFactory = new Log\Repository\Factory();

    return $logRepositoryFactory->get($app['tracker.connection'], 'log');
});

$app['debug'] = $app->share(function () use ($app) {
    return $app['configuration']->get('tracker.debug') === 'true';
});

$app['track.controller'] = $app->share(function() use ($app) {
    return new TrackController(
        $app['log.repository'],
        $app['configuration']->get('tracker.recognizers'),
        $app['configuration']->get('tracker.persistence')
    );
});

$app->get('/', 'track.controller:indexAction');
$app->run();