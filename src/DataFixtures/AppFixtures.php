<?php

namespace App\DataFixtures;

use App\Entity\Habitants;
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
        for($i = 0; $i < 50; $i++){
            $habitants = new Habitants();
            $habitants->setNom($this->faker->lastName());
            $habitants->setPrenom($this->faker->firstName());
            $habitants->setMail($this->faker->email());
            $habitants->setAdresse($this->faker->address());
            $habitants->setMdp($this->faker->password());

            $manager->persist($habitants);
        }
        $manager->flush();

        
    }
}
