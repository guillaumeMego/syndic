<?php

namespace App\Controller;

use App\Entity\Problematiques;
use App\Form\ProblematiquesType;
use App\Entity\SuiviProblematique;
use App\Enum\EtatProblematiqueEnum;
use App\Repository\UserRepository;
use App\Form\EditProblematiquesType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ProblematiquesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SuiviProblematiqueRepository;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ProblematiquesController extends AbstractController
{
    /**
     * Affiche la liste des problématiques
     *
     * @param ProblematiquesRepository $repository
     * @param SuiviProblematiqueRepository $suiviProblematiqueRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/problematiques', name: 'afficher_problematiques', methods: ['GET'])]
    public function show(
        ProblematiquesRepository $repository,
        SuiviProblematiqueRepository $suiviProblematiqueRepository,
        PaginatorInterface $paginator,
        Request $request,
        UserRepository $userRepository
    ): Response {
        $problematiques = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        $problematiquesWithInfo = [];

        foreach ($problematiques as $problematique) {
            $suiviProblematique = $suiviProblematiqueRepository->findOneBy(['problematique' => $problematique]);

            $auteur = $userRepository->findOneBy(['id' => $problematique->getAuteur()->getId()]);

            $problematiquesWithInfo[] = [
                'problematique' => $problematique,
                'suiviProblematique' => $suiviProblematique,
                'auteur' => $auteur,
            ];
        }

        return $this->render('/pages/problematiques/index.html.twig', [
            'problematiques' => $problematiquesWithInfo,
        ]);
    }

    /**
     * Ajoute une problématique
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('problematiques/new', name: 'app_problematiques_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $problematique = new Problematiques();
        $suiviProblematique = new SuiviProblematique();
        $form = $this->createForm(ProblematiquesType::class, $problematique);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $problematique->setAuteur($this->getUser());

            $suiviProblematique->setProblematique($problematique);

            $manager->persist($problematique);

            $manager->persist($suiviProblematique);

            $manager->flush();

            $this->addFlash('success', 'La problématique a bien été ajoutée.');

            return $this->redirectToRoute('afficher_problematiques');
        }

        return $this->render('/pages/problematiques/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('problematiques/edit/{id}', name: 'app_problematiques_edit', methods: ['GET', 'POST'])]
    public function edit(
        Problematiques $problematique,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(EditProblematiquesType::class, [
            'problematique' => $problematique,
            'suiviProblematique' => $problematique->getSuiviProblematique(),
        ]);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrez les modifications dans la base de données
            $entityManager->flush();
    
            $this->addFlash('success', 'La problématique a été modifiée avec succès.');
    
            return $this->redirectToRoute('afficher_problematiques');
        }
    
        return $this->render('pages/problematiques/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime une problématique
     *
     * @param Problematiques $problematique
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('problematiques/delete/{id}', name: 'app_problematiques_delete', methods: ['GET', 'POST'])]
    public function delete(
        Problematiques $problematique,
        EntityManagerInterface $manager
    ): Response {
        $manager->remove($problematique);
        $manager->flush();

        $this->addFlash('success', 'La problématique a bien été supprimée.');

        return $this->redirectToRoute('afficher_problematiques');
    }

    /**
     * Valide une problématique
     *
     * @param Problematiques $problematique
     * @param EntityManagerInterface $manager
     * @param SuiviProblematiqueRepository $suiviProblematiqueRepository
     * @return Response
     */
    #[Route('problematiques/valider/{id}', name: 'app_problematiques_validate', methods: ['GET', 'POST'])]
    public function validate(
        Problematiques $problematique,
        EntityManagerInterface $manager,
        SuiviProblematiqueRepository $suiviProblematiqueRepository
    ): Response {
        $suiviProblematique = $suiviProblematiqueRepository->findOneBy(['problematique' => $problematique]);

        $suiviProblematique->setEtat(EtatProblematiqueEnum::NON_RESOLU);
        $suiviProblematique->setMembreValidateur($this->getUser());

        $manager->persist($suiviProblematique);
        $manager->flush();

        $this->addFlash('success', 'La problématique a bien été validée.');

        return $this->redirectToRoute('afficher_problematiques');
    }
}
