<?php

namespace App\Repository;

use App\Entity\Photo;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use DateTimeImmutable;

/**
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * @param User $user
     * @return mixed
     */
    public function findRegularAndAfterPhotosByUser(User $user)
    {
        $queryBuilder = $this->createQueryBuilder('photo');
        $queryBuilder
            ->where('photo.user = :user')
            ->andWhere('photo.type != :regularStatus')
            ->orderBy('photo.createdAt', 'ASC')
            ->setParameter('user', $user)
            ->setParameter('regularStatus', Photo::BEFORE)
        ;
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    /**
     * @param User $user
     * @param DateTimeImmutable $date
     * @return mixed
     */
    public function findAllPhotosByUserBefore(User $user, DateTimeImmutable $date)
    {
        $queryBuilder = $this->createQueryBuilder('photo');
        $queryBuilder
            ->where('photo.user = :user')
            ->andWhere('photo.createdAt < :date')
            ->setParameter('user', $user)
            ->setParameter('date', $date)
        ;
        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

}
