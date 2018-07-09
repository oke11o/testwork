<?php

namespace App\EventSubscriber;

use App\Mail\TypeFactory\TypeFactory;
use FOS\UserBundle\FOSUserEvents;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use FOS\UserBundle\Event\FilterUserResponseEvent;

class UserRegisterTriggerSubscriber implements EventSubscriberInterface
{
    /**
     * @var ProducerInterface
     */
    private $producer;
    /**
     * @var TypeFactory
     */
    private $typeFactory;
    /**
     * @var int
     */
    private $delay;

    public function __construct(ProducerInterface $producer, TypeFactory $typeFactory, int $delay = 2 * 60 * 60)
    {
        $this->producer = $producer;
        $this->typeFactory = $typeFactory;
        $this->delay = $delay * 1000;
    }

    public function sendTriggerMail(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $request = $event->getRequest();
        $locale = $request ? $request->getLocale() : 'en';
        $type = $this->typeFactory->createMailType($user, $locale);

        $this->producer->publish(\json_encode($type->jsonSerialize()), '', [], ['x-delay' => $this->delay]);
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_COMPLETED => 'sendTriggerMail',
            FOSUserEvents::REGISTRATION_CONFIRMED => 'sendTriggerMail',
        ];
    }
}
