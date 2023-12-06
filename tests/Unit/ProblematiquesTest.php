<?php

namespace App\Tests\Unit;

use App\Entity\Problematiques;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProblematiquesTest extends KernelTestCase
{
    public function getEntity(): Problematiques
    {
        return (new Problematiques())
            ->setProblematique('test')
            ->setDescription('test')
            ->setCommentaire(null);
    }

    public function testEntityIsValid(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $problematique = $this->getEntity();

        $error = $container->get('validator')->validate($problematique);

        $this->assertCount(0, $error);
    }

    public function testInvalidName(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $problematique = $this->getEntity()->setProblematique('');

        $error = $container->get('validator')->validate($problematique);

        $this->assertCount(1, $error);
    }
}
