<?php

namespace App\Exception\Mail;

use App\Mail\Type\AbstractMailType;

class UnavailableTypeException extends MailException
{
    public function __construct(string $className, AbstractMailType $type)
    {
        parent::__construct(sprintf('Factory "%s" not support type "%s"', $className, \get_class($type)));
    }
}