<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Recherche;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @extends ServiceEntityRepository<User>
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findAllWithSearch(Recherche $search)
    {
        $queryBuilder = $this->createQueryBuilder('u');

        if ($search->getQ()) {
            $queryBuilder->where('u.nom LIKE :term')
                ->orWhere('u.prenom LIKE :term')
                ->orWhere('u.email LIKE :term')
                ->orWhere('u.roles LIKE :role')
                ->setParameter('term', '%' . $search->getQ() . '%')
                ->setParameter('role', '%"ROLE_' . strtoupper($search->getQ()) . '"%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * 
     * @param UserPasswordHasherInterface $passwordHasher
     * @param User $user
     * @param string $newHashedPassword
     * @return void
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne le nombre de locataire
     *
     * @return integer
     */
    public function countLocataire(): int
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"' . 'ROLE_LOCATAIRE' . '"%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne le nombre de Proprietaire
     *
     * @return integer
     */
    public function countProprietaire(): int
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"' . 'ROLE_PROPRIETAIRE' . '"%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne le nombre de membre du conseil
     *
     * @return integer
     */
    public function countMembreConseil(): int
    {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"' . 'ROLE_CONSEIL' . '"%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Retourne le role d'un utilisateur
     */
    public function getRole($id)
    {
        return $this->createQueryBuilder('u')
            ->select('u.roles')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
