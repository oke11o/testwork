<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    use TimestampableEntity;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $triggerSent = false;

    public function __construct()
    {
        parent::__construct();
    }

    public function isTriggerSent(): ?bool
    {
        return $this->triggerSent;
    }

    public function setTriggerSent(bool $triggerSent): self
    {
        $this->triggerSent = $triggerSent;

        return $this;
    }
}