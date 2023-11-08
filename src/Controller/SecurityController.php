<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * Formulaire de connexion
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/connexion', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('groupe_page');
        }
        return $this->render('pages/security/login.html.twig', [
            'last_Username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }
    /**
     * Deconnexion
     *
     * @return void
     */
    #[Route('/deconnexion', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // solo
    }

    /**
     * Formulaire d'inscription
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/inscription', name: 'app_registration', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_CONSEIL')]
    public function registration(Request $request, EntityManagerInterface $manager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            if ($form->get('roles')->getData() == 'ROLE_PROPRIETAIRE') {
                $user->setRoles(['ROLE_PROPRIETAIRE']);
            } else {
                $user->setRoles(['ROLE_LOCATAIRE']);
            }



            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success", "Votre compte a bien été créé");
            return $this->redirectToRoute('app_login');
        }

        return $this->render('pages/security/registration.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
