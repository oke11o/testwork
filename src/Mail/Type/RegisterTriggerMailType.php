<?php

namespace App\Mail\Type;

use App\Exception\Mq\InvalidMQMessage;
use App\Mail\MessageFactory\RegisterTriggerMessageFactory;

class RegisterTriggerMailType extends AbstractMailType
{
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $locale;

    public function __construct(string $email, $username, string $locale = 'en')
    {
        $this->email = $email;
        $this->username = $username;
        $this->locale = $locale;
    }

    public function getMessageFactoryName(): string
    {
        return RegisterTriggerMessageFactory::class;
    }

    public function getData()
    {
        return [
            'email' => $this->email,
            'username' => $this->username,
            'locale' => $this->locale,
        ];
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param array $data
     * @return RegisterTriggerMailType
     * @throws \App\Exception\Mq\InvalidMQMessage
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['email'], $data['username'], $data['locale'])) {
            throw new InvalidMQMessage();
        }

        return new self($data['email'], $data['username'], $data['locale']);
    }
}