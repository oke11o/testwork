<?php

namespace App\Command;

use App\Entity\User;
use App\Mail\TypeFactory\TypeFactory;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Stopwatch\Stopwatch;

class UserTriggerWorkerCommand extends Command
{
    protected static $defaultName = 'app:user:trigger-worker';
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ProducerInterface
     */
    private $producer;
    /**
     * @var TypeFactory
     */
    private $typeFactory;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var int
     */
    private $delay;

    public function __construct(EntityManagerInterface $em, ProducerInterface $producer, TypeFactory $typeFactory, int $delay = 2 * 60 * 60)
    {
        parent::__construct();

        $this->em = $em;
        $this->userRepository = $em->getRepository(User::class);
        $this->producer = $producer;
        $this->typeFactory = $typeFactory;
        $this->delay = $delay;
    }

    protected function configure()
    {
        $this->setDescription('Send user trigger mail');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start($this::$defaultName);

        $io = new SymfonyStyle($input, $output);

        $dateInterval = new \DateInterval('PT' . $this->delay . 'S');
        $users = $this->userRepository->findForTriggerSend($dateInterval);
        $count = 0;
        foreach ($users as $user) {
            $count++;
            $type = $this->typeFactory->createMailType($user, 'en');
            $this->producer->publish(\json_encode($type->jsonSerialize()));
            $user->setTriggerSent(true);
        }
        $this->em->flush();

        $event = $stopwatch->stop($this::$defaultName);
        $stopwatchDuration = $event->getDuration() / 1000;

        $io->success(sprintf('Script execution: %f s. Mail sent: %d', $stopwatchDuration, $count));
    }
}
