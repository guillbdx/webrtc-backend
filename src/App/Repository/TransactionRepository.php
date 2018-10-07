<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{

    /**
     * TransactionRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function findByUser(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('transaction');
        $queryBuilder
            ->where('transaction.user = :user')
            ->orderBy('transaction.createdAt', 'DESC')
            ->setParameter('user', $user)
        ;
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

}
