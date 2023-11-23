<?php

namespace App\Repository;

use App\Entity\Recherche;
use App\Entity\SuiviProblematique;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<SuiviProblematique>
 *
 * @method SuiviProblematique|null find($id, $lockMode = null, $lockVersion = null)
 * @method SuiviProblematique|null findOneBy(array $criteria, array $orderBy = null)
 * @method SuiviProblematique[]    findAll()
 * @method SuiviProblematique[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuiviProblematiqueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SuiviProblematique::class);
    }

//    /**
//     * @return SuiviProblematique[] Returns an array of SuiviProblematique objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SuiviProblematique
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
