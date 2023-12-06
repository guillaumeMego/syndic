<?php

namespace App\Tests\Fonctional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class ContactTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/connexion');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Formulaire de connexion :');

        //Recuperer le formulaire
        $submitButton = $crawler->selectButton('Se connecter');
        $form = $submitButton->form();

        //Remplir le formulaire
        $form['_username'] = 'test@tests.com';
        $form['_password'] = 'test';
        
        //Soummettre l'envoi du mail 
        $client->submit($form);

        //Verifier le statut HTTP
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
    }
}
