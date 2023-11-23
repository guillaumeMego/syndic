<?php

namespace App\Repository;

use App\Entity\Recherche;
use App\Entity\Problematiques;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

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

    public function findAllWithSearch(Recherche $search)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->leftJoin('p.suiviProblematiques', 's')
            ->leftJoin('p.auteur', 'a');

        if ($search->getQ()) {
            $queryBuilder->where('LOWER(p.problematique) LIKE :term')
                ->orWhere('LOWER(p.description) LIKE :term')
                ->orWhere('LOWER(s.etat) LIKE :term')
                ->orWhere('LOWER(a.nom) LIKE :term') 
                ->orWhere('LOWER(a.prenom) LIKE :term')
                ->setParameter('term', '%' . strtolower($search->getQ()) . '%');
        }

        return $queryBuilder->getQuery();
    }

    //    /**
    //     * @return Problematiques[] Returns an array of Problematiques objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p's)
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
