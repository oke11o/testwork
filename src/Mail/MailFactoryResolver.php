<?php

namespace App\Mail;

use App\Exception\Mail\InvalidFactoryNameException;
use App\Mail\MessageFactory\AbstractMessageFactory;
use App\Mail\MessageFactory\RegisterTriggerMessageFactory;
use App\Mail\Type\AbstractMailType;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceSubscriberInterface;

class MailFactoryResolver implements ServiceSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param AbstractMailType $type
     * @return AbstractMessageFactory
     *
     * @throws \App\Exception\Mail\InvalidFactoryNameException
     */
    public function getMessageFactory(AbstractMailType $type): AbstractMessageFactory
    {
        if (!\in_array($type->getMessageFactoryName(), self::getSubscribedServices(), true)) {
            throw new InvalidFactoryNameException($type);
        }

        $service = $this->container->get($type->getMessageFactoryName());

        if ($service instanceof AbstractMessageFactory) {
            return $service;
        }

        throw new InvalidFactoryNameException($type);
    }

    /**
     * @return array The required service types, optionally keyed by service names
     */
    public static function getSubscribedServices()
    {
        return [
            RegisterTriggerMessageFactory::class,
        ];
    }
}