<?php

$startTime = microtime(true);

require_once __DIR__.'/../vendor/autoload.php';

use Mindweb\Config;
use Mindweb\Tracker\Controller\TrackController;
use Mindweb\Tracker\Loader\SubscribersLoader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

$app = new Silex\Application();
$app['startTime'] = $app->share(function () use ($startTime) {
    return $startTime;
});

$app['configuration'] = $app->share(function () {
    return Config\ConfigurationFactory::factory(
        'Mindweb',
        'ConfigJson',
        '../config.json'
    );
});

$app['debug'] = $app->share(function () use ($app) {
    return $app['configuration']->get('tracker.debug') === 'true';
});

$app['subscribers_loader'] = $app->share(function () use ($app) {
    /**
     * @var Config\Configuration $configuration
     */
    $configuration = $app['configuration'];

    /**
     * @var EventDispatcherInterface $dispatcher
     */
    $dispatcher = $app['dispatcher'];

    return new SubscribersLoader(
        $configuration,
        $dispatcher,
        array_keys(
            $configuration->get('tracker.subscribers')
        ),
        'tracker.subscribers'
    );
});

$app['track.controller'] = $app->share(function() use ($app) {
    $app['subscribers_loader']->load($app);

    return new TrackController();
});

$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->get('/', 'track.controller:indexAction');
$app->run();