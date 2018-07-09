<?php

namespace App\Exception\Mail;

use App\Mail\Type\AbstractMailType;

class InvalidFactoryNameException extends MailException
{
    public function __construct(AbstractMailType $type)
    {
        parent::__construct(sprintf('Invalid factory name in Type ("%s")', \get_class($type)));
    }
}