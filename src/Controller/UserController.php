<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Recherche;
use App\Form\RechercheType;
use App\Form\UserPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    /**
     * Affichage des residents
     *
     * @param UserRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/resident/afficher', name: 'afficher_residents', methods: ['GET'])]
    #[IsGranted('ROLE_LOCATAIRE')]
    public function afficherResident(
        UserRepository $repository,
        PaginatorInterface $paginator,
        Request $request,
    ): Response {

        $search = new Recherche();
        $form = $this->createForm(RechercheType::class, $search);
        $form->handleRequest($request);

        $queryBuilder = $repository->createQueryBuilder('u');

        if ($search->getQ()) {
            $query = $repository->findAllWithSearch($search);

            $users = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                10
            );
        } else {

            $users = $paginator->paginate(
                $queryBuilder->getQuery(),
                $request->query->getInt('page', 1),
                10
            );
        }

        return $this->render('pages/residents/afficher.html.twig', [
            'users' => $users,
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
    #[Route('/resident/modifier-mot-de-passe/{id}', name: 'app_edit_password', methods: ['GET', 'POST'])]
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
            $this->addFlash('danger', 'Vous ne pouvez pas modifier le mot de passe d\'un autre utilisateur');
            return $this->redirectToRoute('afficher_residents');
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

                return $this->redirectToRoute('edit_my_profile', [
                    'id' => $user->getId()
                ]);
            } else {
                $this->addFlash('danger', 'Le mot de passe actuel est incorrect');
            }
        }

        return $this->render('pages/residents/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edition du profil
     *
     * @param User $user
     * @param Request $request
     * @return Response
     */
    #[Route('/resident/edition/{id}', name: 'app_edit', methods: ['GET', 'POST'])]
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

            if ($form->get('roles')->getData()) {
                $user->setRoles(['ROLE_PROPRIETAIRE', 'ROLE_CONSEIL']);
            } else {
                $user->setRoles(['ROLE_PROPRIETAIRE']);
            }

            $user->setDateModif(new \DateTimeImmutable());
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', 'Le profil a bien été modifié');

            return $this->redirectToRoute('afficher_residents');
        }

        return $this->render('pages/residents/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Suppression d'un resident
     *
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/resident/supprimer/{id}', name: 'supprimer_resident', methods: ['GET'])]
    #[IsGranted('ROLE_CONSEIL')]
    public function delete(
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success', 'Le resident a bien été supprimé');
        return $this->redirectToRoute('afficher_residents');
    }

    /**
     * Editer le profil de l'utilisateur connecté
     *
     * @param User $user
     * @return Response
     */
    #[Route('/resident/profil/{id}', name: 'edit_my_profil', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_LOCATAIRE')]
    public function profil(
        User $user,
        Request $request,
        EntityManagerInterface $manager,
    ): Response {

        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $user = $form->getData();

                $user->setDateModif(new \DateTimeImmutable());

                if ($form->get('roles')->getData()) {
                    $user->setRoles(['ROLE_PROPRIETAIRE', 'ROLE_CONSEIL']);
                } else {
                    $user->setRoles(['ROLE_PROPRIETAIRE']);
                }

                $manager->persist($user);

                $manager->flush();


                $user->setImageFile(null);
                $this->addFlash('success', 'Votre profil a bien été modifié');

                return $this->redirectToRoute('edit_my_profil', [
                    'id' => $user->getId()
                ]);
            } else {
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->render('pages/residents/profil.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Affichage du profil d'un resident par l'id
     * 
     * @param User $user
     * @return Response
     */
    #[Route('/resident/{id}', name: 'voir_resident', methods: ['GET'])]
    #[IsGranted('ROLE_CONSEIL')]
    public function voirResident(
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        $resident = $entityManager->getRepository(User::class)->find($user);

        $roleConseil = $entityManager->getRepository(User::class)->findOneBy(['id' => $user, 'roles' => 'ROLE_CONSEIL']);

        return $this->render('pages/residents/resident.html.twig', [
            'resident' => $resident,
            'roleConseil' => $roleConseil
        ]);
    }
}
