<?php

require_once __DIR__.'/../vendor/autoload.php';

use Mindweb\Log;
use Mindweb\Config;
use Mindweb\Db;

$app = new Silex\Application();

$configuration = Config\ConfigurationFactory::factory('Mindweb', 'ConfigJson', '../config.json');

$logConnection = Db\ConnectionFactory::create($configuration, 'db.type');
$logRepositoryFactory = new Log\Repository\Factory();
$app['log.repository'] = $logRepositoryFactory->get($logConnection, 'log');

$app['debug'] = $configuration->get('tracker.debug') === 'true';

$app->get('/', function () use ($app) {
    $start = microtime(true);

    /**
     * @var Symfony\Component\HttpFoundation\Request $request
     */
    $request = $app['request'];

    $userAgent = $request->headers->get('User-Agent');
    $referrerUrl = $request->get('referrer');
    $returning = $request->get('returning') === '1';
    $actionTime = gmdate('Y-m-d H:i:s');

    /**
     * @var Log\Repository\LogRepository $logRepository
     */
    $logRepository = $app['log.repository'];

    $logRepository->insert(
        $actionTime,
        array(
            $userAgent,
            $referrerUrl,
            $returning
        )
    );

    $executionTime = (microtime(true) - $start) * 1000;

    return $executionTime;
});

$app->run();