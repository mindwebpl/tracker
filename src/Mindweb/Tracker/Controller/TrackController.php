<?php
namespace Mindweb\Tracker\Controller;

use Mindweb\Recognizer\Recognizer;
use Mindweb\Recognizer\Event\AttributionEvent;
use Mindweb\Persist\Persist;
use Mindweb\Persist\Event\PersistEvent;
use Mindweb\Resolve\Resolve;
use Mindweb\Resolve\Event\ResolveEvent;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackController
{
    /**
     * @param Application $app
     * @return Response
     */
    public function indexAction(Application $app)
    {
        /**
         * @var EventDispatcherInterface $dispatcher
         */
        $dispatcher = $app['dispatcher'];

        /**
         * @var Request $request
         */
        $request = $app['request'];

        $attributionEvent = new AttributionEvent($request);
        $dispatcher->dispatch(Recognizer::RECOGNIZE_EVENT, $attributionEvent);

        $persistEvent = new PersistEvent($attributionEvent);
        $dispatcher->dispatch(Persist::PERSIST_EVENT, $persistEvent);

        $response = new Response();
        $resolveEvent = new ResolveEvent($attributionEvent, $response);
        $dispatcher->dispatch(Resolve::RESOLVE_EVENT, $resolveEvent);

        return $response;
    }
} 