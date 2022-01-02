<?php

namespace App\Repository;

use App\Entity\EditPicture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EditPicture|null find($id, $lockMode = null, $lockVersion = null)
 * @method EditPicture|null findOneBy(array $criteria, array $orderBy = null)
 * @method EditPicture[]    findAll()
 * @method EditPicture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EditPictureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EditPicture::class);
    }

    // /**
    //  * @return EditPicture[] Returns an array of EditPicture objects
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
    public function findOneBySomeField($value): ?EditPicture
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
