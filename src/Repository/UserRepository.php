<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findForTriggerSend(\DateInterval $interval): array
    {
        $date = (new \DateTime())->sub($interval);

        $qb = $this->createQueryBuilder('u')
            ->andWhere('u.createdAt < :date')
            ->andWhere('u.triggerSent = :triggerSent')
            ->setParameters(
                [
                    'date' => $date,
                    'triggerSent' => false,
                ]
            )
            ->orderBy('u.id', 'ASC');

        return $qb->getQuery()->getResult();
    }

}
