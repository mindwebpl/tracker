<?php
require_once __DIR__.'/../vendor/autoload.php';

use Mindweb\TrackerKernelStandard as Kernel;
use Mindweb\TrackerKernel as KernelAdapter;

$env = !empty($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : 'prod';
$debug = !empty($_SERVER['APP_DEBUG']) && $_SERVER['APP_DEBUG'] === 'true';
$configPath = !empty($_SERVER['APP_CONFIG_PATH']) ? $_SERVER['APP_CONFIG_PATH'] : '../app/config';
$cachePath = !empty($_SERVER['APP_CACHE_PATH']) ? $_SERVER['APP_CACHE_PATH'] : '../app/cache';

$config = new Kernel\Configuration\File(
    new SplFileInfo($configPath . '/config_%s.json')
);
$cache = new Kernel\Configuration\Cache(
    new SplFileInfo($cachePath . '/config.php')
);

$subscribersCache = new Kernel\Configuration\Cache(
    new SplFileInfo($cachePath . '/subscribers.php')
);

$subscribersLoaderCache = new Kernel\Configuration\Cache(
    new SplFileInfo($cachePath . '/subscribers_%s.php')
);
$subscribersLoader = new Kernel\Subscriber\Loader($subscribersLoaderCache, $debug);

try {
    $kernel = new Kernel\Kernel($env, $debug);
    $kernel->loadConfiguration($config, $cache);
    $kernel->registerSubscribers($subscribersLoader, $subscribersCache);
    $kernel->registerEndPoint();
    $kernel->run();
} catch (\Exception $e) {
    if ($debug) {
        print nl2br($e->getTraceAsString());
    } else {
        header('HTTP/1.1 500 Internal Server Error', true, 500);
        trigger_error($e->getMessage(), E_USER_ERROR);
    }

    exit (1);
}