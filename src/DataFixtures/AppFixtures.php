<?php

namespace App\DataFixtures;

use App\Entity\user;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
{
    // Créer 10 propriétaires
        for ($i = 0; $i < 10; $i++) {
            $user = $this->createUser();
            $user->setRoles(['ROLE_PROPRIETAIRE']);
            $manager->persist($user);
        }

        // Créer 2 propriétaires membres du conseil
        for ($i = 0; $i < 2; $i++) {
            $user = $this->createUser();
            $user->setRoles(['ROLE_PROPRIETAIRE', 'ROLE_CONSEIL']);
            $manager->persist($user);
        }

        // Créer 48 locataires
        for ($i = 0; $i < 48; $i++) {
            $user = $this->createUser();
            $user->setRoles(['ROLE_LOCATAIRE']);
            $manager->persist($user);
        }

        $manager->flush();

        
}
private function createUser()
    {
        $user = new User();
        $user->setNom($this->faker->lastName());
        $user->setPrenom($this->faker->firstName());
        $user->setEmail($this->faker->email());
        $user->setAdresse($this->faker->address());
        $user->setPlainPassword('password');

        return $user;
    }

}
