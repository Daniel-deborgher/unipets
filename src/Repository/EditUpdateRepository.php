<?php

namespace App\Repository;

use App\Entity\EditUpdate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditUpdate|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditUpdate|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditUpdate[]    findAll()
 * @method EditUpdate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditUpdateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditUpdate::class);
    }

    // /**
    //  * @return EditUpdate[] Returns an array of EditUpdate objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EditUpdate
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
