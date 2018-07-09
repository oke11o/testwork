<?php

namespace App\Mail\TypeFactory;

use App\Entity\User;
use App\Exception\Mq\InvalidMQMessage;
use App\Mail\Type\AbstractMailType;
use App\Mail\Type\RegisterTriggerMailType;

class TypeFactory
{
    /**
     * @param User $user
     * @param $locale
     * @return RegisterTriggerMailType
     */
    public function createMailType(User $user, $locale)
    {
        return new RegisterTriggerMailType($user->getEmailCanonical(), $user->getUsername(), $locale);
    }

    /**
     * @param string $json
     * @return AbstractMailType
     * @throws \App\Exception\Mq\InvalidMQMessage
     */
    public function unserializeType(string $json): AbstractMailType
    {
        $data = json_decode($json, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidMQMessage('Unable to parse json: '.json_last_error_msg());
        }


        if (!isset($data['type'])) {
            throw new InvalidMQMessage(sprintf('Invalid type %s', $json));
        }

        $type = $data['type'];

        try {
            return $type::fromArray($data['data']);
        } catch (\Exception $exception) {
            throw new InvalidMQMessage('Cannot create type from json: '.json_last_error_msg());
        }
    }
}