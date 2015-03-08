<?php
namespace Mindweb\Tracker\Controller;

use Mindweb\Log\Repository\LogRepository;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackController
{
    /**
     * @var LogRepository
     */
    private $logRepository;

    /**
     * @param LogRepository $logRepository
     * @param array $recognizers
     * @param array $persistence
     */
    public function __construct(LogRepository $logRepository, array $recognizers, array $persistence)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * @param Application $app
     * @return Response
     */
    public function indexAction(Application $app)
    {
        $start = microtime(true);

        /**
         * @var Request $request
         */
        $request = $app['request'];

        $userAgent = $request->headers->get('User-Agent');
        $referrerUrl = $request->get('referrer');
        $returning = $request->get('returning') === '1';
        $actionTime = gmdate('Y-m-d H:i:s');

        $this->logRepository->insert(
            $actionTime,
            array(
                $userAgent,
                $referrerUrl,
                $returning
            )
        );

        $executionTime = (microtime(true) - $start) * 1000;

        return new Response($executionTime);
    }
} 