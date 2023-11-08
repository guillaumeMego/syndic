<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
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
    #[IsGranted('ROLE_CONSEIL')]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $manager
    ): Response {

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            // si case cochée, on ajoute le rôle conseil
            if ($form->get('roles')->getData()) {
                $user->setRoles(['ROLE_PROPRIETAIRE', 'ROLE_CONSEIL']);
            } else {
                $user->setRoles(['ROLE_PROPRIETAIRE']);
            }


            $user->setDateModif(new \DateTimeImmutable());

            $manager->persist($user);

            $manager->flush();

            $this->addFlash('success', 'Votre profil a bien été modifié');

            return $this->redirectToRoute('afficher_utilisateur');
        }

        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
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
    #[IsGranted('ROLE_LOCATAIRE')]
    public function editPassword(
        User $user,
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $manager
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('home.index');
        }

        $form = $this->createForm(UserPasswordType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()['plainPassword'])) {
                $user->setDateModif(new \DateTimeImmutable());
                $user->setPlainPassword($form->getData()['newPassword']);

                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'Votre mot de passe a bien été modifié');

                return $this->redirectToRoute('home.index');
            } else {
                $this->addFlash('danger', 'Le mot de passe actuel est incorrect');
            }
        }

        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Affichage des utilisateurs
     *
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/utilisateur/afficher', name: 'afficher_utilisateur', methods: ['GET'])]
    #[IsGranted('ROLE_LOCATAIRE')]
    public function show(UserRepository $repository,
    PaginatorInterface $paginator,
    Request $request): Response
    {
        $users = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), 
            10
        );
        return $this->render('pages/user/show.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Suppression d'un utilisateur
     *
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/utilisateur/supprimer/{id}', name: 'supprimer_utilisateur', methods: ['GET'])]
    #[IsGranted('ROLE_CONSEIL')]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', 'L\'utilisateur a bien été supprimé');
        return $this->redirectToRoute('afficher_utilisateur');
    }

    /**
     * Affichage du profil
     *
     * @param User $user
     * @return Response
     */
    #[Route('/utilisateur/profil/{id}', name: 'edit_my_profil', methods: ['GET'])]
    #[IsGranted('ROLE_LOCATAIRE')]
    public function profil(User $user): Response
    {
        $user = $this->getUser();

        return $this->render('pages/user/profil.html.twig', [
            'user' => $user,
        ]);
    }
}
