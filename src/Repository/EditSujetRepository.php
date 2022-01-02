<?php

namespace App\Repository;

use App\Entity\EditSujet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditSujet|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditSujet|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditSujet[]    findAll()
 * @method EditSujet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditSujetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditSujet::class);
    }

    // /**
    //  * @return EditSujet[] Returns an array of EditSujet objects
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
    public function findOneBySomeField($value): ?EditSujet
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
