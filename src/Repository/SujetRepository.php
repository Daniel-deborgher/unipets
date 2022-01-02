<?php

namespace App\Repository;

use App\Entity\Sujet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sujet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sujet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sujet[]    findAll()
 * @method Sujet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SujetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sujet::class);
    }

    // /**
    //  * @return Sujet[] Returns an array of Sujet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sujet
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByLastCategory($valeur)
    {
        return $this->createQueryBuilder('a')
            ->where('a.category = :val')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(6)
            ->setParameter('val', $valeur)
            ->getQuery()
            ->getResult()
        ;
    } 
    public function findByWord ($mot) # faire une recherche par un mot, convention : Find
    {
        return $this->createQueryBuilder('a') # récupérer tout les produits
            ->where('a.titre LIKE :val') # récupérer tout les titres avec le mot LIKE = ressemble
            // ->andWhere('p.exampleField = :val') faire andWhere pour d'autres condition
            ->setParameter('val',"%" . $mot . "%")
            ->orderBy('a.titre', 'DESC') # méthode ascendente selon le titre
            ->addOrderBy("a.category") #deuxième paramètre order, null, ASC de base
            ->getQuery()
            ->getResult()
        ;
    }
    public function findByCategory($valeur)
    {
        return $this->createQueryBuilder('a')
            ->where('a.category = :val')
            ->orderBy('a.createdAt', 'DESC')
            ->setParameter('val', $valeur)
            ->getQuery()
            ->getResult()
        ;
    } 
}
