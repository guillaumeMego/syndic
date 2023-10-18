<?php

namespace App\Repository;

use App\Entity\Habitants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Habitants>
 *
 * @method Habitants|null find($id, $lockMode = null, $lockVersion = null)
 * @method Habitants|null findOneBy(array $criteria, array $orderBy = null)
 * @method Habitants[]    findAll()
 * @method Habitants[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HabitantsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Habitants::class);
    }

//    /**
//     * @return Habitants[] Returns an array of Habitants objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Habitants
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
