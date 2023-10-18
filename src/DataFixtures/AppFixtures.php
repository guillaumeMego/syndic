<?php

namespace App\DataFixtures;

use App\Entity\user;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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
        for($i = 0; $i < 10; $i++){
            $user = new User();
            $user->setNom($this->faker->lastName());
            $user->setPrenom($this->faker->firstName());
            $user->setEmail($this->faker->email());
            $user->setRoles(['ROLE_USER']);
            $user->setAdresse($this->faker->address());
            $user->setPlainPassword('password');
        

            $manager->persist($user);
        }
        $manager->flush();

        
    }
}
