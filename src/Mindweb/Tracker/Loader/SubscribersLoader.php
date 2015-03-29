<?php
namespace Mindweb\Tracker\Loader;

use Mindweb\Config\Configuration;
use Mindweb\Tracker\Exception\SubscriberDoesNotExists;
use Silex\Application;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscribersLoader
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var array
     */
    private $subscriberTypes;

    /**
     * @var string
     */
    private $configurationPrefix;

    /**
     * @param Configuration $configuration
     * @param EventDispatcherInterface $dispatcher
     * @param array $subscriberTypes
     * @param string $configurationPrefix
     */
    public function __construct(Configuration $configuration, EventDispatcherInterface $dispatcher,
                                array $subscriberTypes, $configurationPrefix)
    {
        $this->configuration = $configuration;
        $this->dispatcher = $dispatcher;
        $this->subscriberTypes = $subscriberTypes;
        $this->configurationPrefix = $configurationPrefix;
    }

    /**
     * @param Application $application
     * @throws SubscriberDoesNotExists
     */
    public function load(Application $application)
    {
        foreach ($this->subscriberTypes as $type) {
            $key = $this->configurationPrefix . '.' . $type;

            /**
             * @var EventSubscriberInterface $subscriber
             */
            foreach ($this->configuration->get($key) as $subscriber) {
                if (!class_exists($subscriber)) {
                    throw new SubscriberDoesNotExists($subscriber, $type);
                }

                $this->dispatcher->addSubscriber(
                    new $subscriber($application)
                );
            }
        }
    }
} 