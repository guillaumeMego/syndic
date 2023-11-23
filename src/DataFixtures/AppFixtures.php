<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\user;
use Faker\Generator;
use DateTimeImmutable;
use App\Entity\Problematiques;
use App\Entity\SuiviProblematique;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

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
        // Variable pour suivre le nombre total d'utilisateurs créés
        $userCount = 0;

        // Créer 10 propriétaires
        for ($i = 0; $i < 10; $i++) {
            $user = $this->createUser();
            $user->setRoles(['ROLE_PROPRIETAIRE']);
            $manager->persist($user);
            $this->addReference('user_' . $userCount, $user);
            $userCount++;
        }

        // Créer 2 propriétaires membres du conseil
        for ($i = 0; $i < 5; $i++) {
            $user = $this->createUser();
            $user->setRoles(['ROLE_PROPRIETAIRE', 'ROLE_CONSEIL']);
            $manager->persist($user);
            $this->addReference('user_' . $userCount, $user);
            $userCount++;
        }

        // Créer 48 locataires
        for ($i = 0; $i < 30; $i++) {
            $user = $this->createUser();
            $user->setRoles(['ROLE_LOCATAIRE']);
            $manager->persist($user);
            $this->addReference('user_' . $userCount, $user);
            $userCount++;
        }

        // Créer 30 problématiques
        for ($i = 0; $i < 60; $i++) {
            $problematique = $this->createProblematique($userCount, $manager);
            $manager->persist($problematique);
        }

        $manager->flush();
    }

    private function createProblematique($userCount, ObjectManager $manager)
    {
        $problematique = new Problematiques();
        $problematique->setProblematique($this->faker->sentence());
        $problematique->setDescription($this->faker->text());
        $problematique->setImageName($this->faker->image('public/images/problematiques', 640, 480, null, false));
        $problematique->setDateAjout(DateTimeImmutable::createFromMutable($this->faker->dateTimeBetween('-5 years')));
        $problematique->setDateModif(DateTimeImmutable::createFromMutable($this->faker->dateTime('-3 months')));

        // Utiliser une référence d'utilisateur existant
        $problematique->setAuteur($this->getReference('user_' . rand(0, $userCount - 1)));

        // Créer et configurer le SuiviProblematique
        // changer le statut de la problématique au hasard
        
        $suiviProblematique = new SuiviProblematique();
        $suiviProblematique->setEtat($this->faker->randomElement(['En attente de validation', 'En cours', 'Résolu', 'Non résolu']));
        $suiviProblematique->setMembreValidateur($this->getReference('user_' . rand(0, $userCount - 1)));
        $suiviProblematique->setProblematique($problematique);
        // Configurez d'autres propriétés de SuiviProblematique ici si nécessaire

        // Persister le suivi de la problématique
        $manager->persist($suiviProblematique);

        return $problematique;
    }

    private function createUser()
    {
        $user = new User();
        $user->setNom($this->faker->lastName());
        $user->setPrenom($this->faker->firstName());
        $user->setEmail($this->faker->email());
        $user->setBatiment($this->faker->randomElement(['A', 'B', 'C']));
        $user->setEtage($this->faker->numberBetween(1, 10));
        $user->setNumeroAppartement($this->faker->numberBetween(1, 100));
        $user->setTelephone($this->faker->regexify('/^0[1-9]([0-9]{2}){4}$/'));
        $user->setDateAjout(DateTimeImmutable::createFromMutable($this->faker->dateTime('-3 years')));
        $user->setDateModif(DateTimeImmutable::createFromMutable($this->faker->dateTime('-2 years')));
        $user->setPasswordChangeRequired(false);
        $user->setIsVerified(true);
        $user->setPlainPassword('password');

        return $user;
    }
}
