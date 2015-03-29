<?php
namespace Mindweb\Tracker\Exception;

use Exception;

class SubscriberDoesNotExists extends Exception
{
    public function __construct($subscriberClassName, $type)
    {
        parent::__construct(
            sprintf(
                'Subscriber %s for %s doesn\'t exists.',
                $subscriberClassName,
                $type
            )
        );
    }
} 