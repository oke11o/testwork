<?php

namespace App\Mail;

use App\Exception\Mail\MailException;
use App\Mail\Type\AbstractMailType;
use Psr\Log\LoggerInterface;

class Mailer
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var MailFactoryResolver
     */
    private $resolver;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(\Swift_Mailer $mailer, MailFactoryResolver $resolver, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->resolver = $resolver;
        $this->logger = $logger;
    }

    /**
     * @param AbstractMailType $type
     * @return int
     */
    public function send(AbstractMailType $type): int
    {
        $result = 0;
        try {
            $messageFactory = $this->resolver->getMessageFactory($type);
            $message = $messageFactory->create($type);
            $message->setTo($type->getEmail());

            $result = $this->mailer->send($message);
        } catch(MailException $e) {
            $this->logger->error($e->getMessage());
        }

        return $result;
    }
}