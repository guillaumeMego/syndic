<?php

namespace App\Repository;

use App\Entity\Problematiques;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Problematiques>
 *
 * @method Problematiques|null find($id, $lockMode = null, $lockVersion = null)
 * @method Problematiques|null findOneBy(array $criteria, array $orderBy = null)
 * @method Problematiques[]    findAll()
 * @method Problematiques[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProblematiquesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Problematiques::class);
    }

//    /**
//     * @return Problematiques[] Returns an array of Problematiques objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Problematiques
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
