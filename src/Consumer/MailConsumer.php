<?php

namespace App\Consumer;

use App\Exception\Mail\InvalidFactoryNameException;
use App\Exception\Mq\InvalidMQMessage;
use App\Mail\Mailer;
use App\Mail\TypeFactory\TypeFactory;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SwiftmailerBundle\EventListener\EmailSenderListener;

class MailConsumer implements ConsumerInterface
{
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var TypeFactory
     */
    private $typeFactory;
    /**
     * @var EmailSenderListener
     */
    private $emailSenderListener;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Mailer $mailer,
        TypeFactory $typeFactory,
        EmailSenderListener $emailSenderListener,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
        $this->typeFactory = $typeFactory;
        $this->emailSenderListener = $emailSenderListener;
        $this->logger = $logger;
    }

    /**
     * @param AMQPMessage $msg The message
     * @return mixed false to reject and requeue, any other value to acknowledge
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function execute(AMQPMessage $msg)
    {
        $redelivery = false;
        try {
            $json = $msg->getBody();
            $type = $this->typeFactory->unserializeType($json);

            $this->mailer->send($type);

            $this->emailSenderListener->onTerminate();
        } catch (InvalidMQMessage|InvalidFactoryNameException $e) {
            $this->logger->error($e->getMessage());
        }

        return $redelivery ? ConsumerInterface::MSG_REJECT_REQUEUE : ConsumerInterface::MSG_ACK;
    }
}