<?php

namespace Tests\Unit\App\EventSubscriber;

use App\Entity\User;
use App\EventSubscriber\UserRegisterTriggerSubscriber;
use App\Mail\Type\RegisterTriggerMailType;
use App\Mail\TypeFactory\TypeFactory;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;

class UserRegisterTriggerSubscriberTest extends \PHPUnit\Framework\TestCase
{
    private const DELAY = 234234;
    /**
     * @var ProducerInterface|ObjectProphecy
     */
    private $producer;
    /**
     * @var TypeFactory|ObjectProphecy
     */
    private $typeFactory;
    /**
     * @var UserRegisterTriggerSubscriber
     */
    private $subscriber;

    public function setUp()
    {
        $this->producer = $this->prophesize(ProducerInterface::class);
        $this->typeFactory = $this->prophesize(TypeFactory::class);
        $this->subscriber = new UserRegisterTriggerSubscriber(
            $this->producer->reveal(),
            $this->typeFactory->reveal(),
            self::DELAY
        );
    }

    /**
     * @test
     */
    public function getSubscribedEvents()
    {
        $this->assertEquals(
            [
                'fos_user.registration.completed' => 'sendTriggerMail',
                'fos_user.registration.confirmed' => 'sendTriggerMail',
            ],
            $this->subscriber::getSubscribedEvents()
        );
    }

    /**
     * @test
     */
    public function sendTriggerMail()
    {
        $event = $this->prophesize(FilterUserResponseEvent::class);
        $user = new User();
        $event->getUser()->shouldBeCalled()->willReturn($user);

        $request = $this->prophesize(Request::class);
        $event->getRequest()->shouldBeCalled()->willReturn($request);

        $locale = 'ru';
        $request->getLocale()->shouldBeCalled()->willReturn($locale);

        $type = $this->prophesize(RegisterTriggerMailType::class);
        $data = ['asdfasdf' => 'vxzcvzcxv'];
        $type->jsonSerialize()->shouldBeCalled()->willReturn($data);
        $this->typeFactory->createMailType($user, $locale)->shouldBeCalled()->willReturn($type->reveal());

        $this->producer->publish(\json_encode($data), '', [], ['x-delay' => self::DELAY]);

        $this->subscriber->sendTriggerMail($event->reveal());
    }
}
