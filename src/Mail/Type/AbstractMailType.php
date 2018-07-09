<?php

namespace App\Mail\Type;

use JsonSerializable;

abstract class AbstractMailType implements JsonSerializable
{
    abstract public function getMessageFactoryName(): string;

    abstract public function getData();

    abstract public function getEmail();

    abstract public function getLocale(): string;


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'type' => static::class,
            'data' => $this->getData()
        ];
    }

    abstract public static function fromArray(array $data);
}