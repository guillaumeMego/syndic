<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * Edition du profil
     *
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/utilisateur/edition/{id}', name: 'app_edit', methods: ['GET', 'POST'])]
    public function edit(
        User $user, 
        Request $request, 
        EntityManagerInterface $manager): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        if($this->getUser() !== $user)
        {
            return $this->redirectToRoute('app_habitants');
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            $manager->persist($user);

            $manager->flush();

            $this->addFlash('success', 'Votre profil a bien été modifié');

            return $this->redirectToRoute('app_habitants');
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modification du mot de passe
     *
     * @param User $user
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/utilisateur/modifier-mot-de-passe/{id}', name: 'app_edit_password', methods: ['GET', 'POST'])]
    public function editPassword(
        User $user, 
        Request $request, 
        UserPasswordHasherInterface $hasher, 
        EntityManagerInterface $manager): Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        if($this->getUser() !== $user)
        {
            return $this->redirectToRoute('app_habitants');
        }

        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            if($hasher->isPasswordValid($user, $form->getData()['plainPassword']))
            {
                $user->setDateModif(new \DateTimeImmutable());
                $user->setPlainPassword($form->getData()['newPassword']);

                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été modifié');

                return $this->redirectToRoute('app_habitants');
            }
            else
            {
                $this->addFlash('danger', 'Le mot de passe actuel est incorrect');
            }
        }
        
        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
