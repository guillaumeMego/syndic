<?php

namespace App\Controller;

use App\Entity\Recherche;
use App\Form\RechercheType;
use App\Entity\Problematiques;
use App\Form\CombinedFormType;
use App\Form\ProblematiquesType;
use App\Entity\SuiviProblematique;
use App\Repository\UserRepository;
use App\Enum\EtatProblematiqueEnum;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ProblematiquesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SuiviProblematiqueRepository;
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
    public function afficher(
        ProblematiquesRepository $repository,
        SuiviProblematiqueRepository $suiviProblematiqueRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $search = new Recherche();
        $form = $this->createForm(RechercheType::class, $search);
        $form->handleRequest($request);

        $problematiques = $repository->findAll();

        $suiviProblematiques = $suiviProblematiqueRepository->findBy(['etat' => EtatProblematiqueEnum::EN_ATTENTE]);

        $suiviProblematiquesReste = $suiviProblematiqueRepository->findBy(['etat' => [
            EtatProblematiqueEnum::EN_COURS,
            EtatProblematiqueEnum::RESOLU,
            EtatProblematiqueEnum::NON_RESOLU,
        ]]);

        $problematiquesWithSuivi = [];
        foreach ($problematiques as $problematique) {
            foreach ($suiviProblematiques as $suiviProblematique) {
                if ($problematique->getId() == $suiviProblematique->getProblematique()->getId()) {
                    $problematique->setSuiviProblematiques($suiviProblematique);
                    $problematiquesWithSuivi[] = $problematique;
                }
            }
        }

        $problematiqueReste = [];
        foreach ($problematiques as $problematique) {
            foreach ($suiviProblematiquesReste as $suiviProblematiqueReste) {
                if ($problematique->getId() == $suiviProblematiqueReste->getProblematique()->getId()) {
                    $problematique->setSuiviProblematiques($suiviProblematiqueReste);
                    $problematiqueReste[] = $problematique;
                }
            }
        }

        if ($search->getQ()) {

            $query = $repository->findAllWithSearch($search);

            $problematiquesPaginated = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                10
            );

            $suiviProblematiquesRestePaginated = $paginator->paginate(
                $query,
                $request->query->getInt('page1', 1),
                6,
                ['pageParameterName' => 'page1']
            );
        } else {
            $suiviProblematiquesRestePaginated = $paginator->paginate(
                $problematiqueReste,
                $request->query->getInt('page1', 1),
                6,
                ['pageParameterName' => 'page1']
            );

            $problematiquesPaginated = $paginator->paginate(
                $problematiquesWithSuivi,
                $request->query->getInt('page2', 1),
                6,
                ['pageParameterName' => 'page2']
            );
        }

        return $this->render('/pages/problematiques/index.html.twig', [
            'problematiques' => $problematiquesPaginated,
            'suiviProblematiquesReste' => $suiviProblematiquesRestePaginated,
            'problematiquesPaginated' => $problematiquesPaginated,
            'form' => $form->createView(),
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
    public function ajoutProlematique(
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $problematique = new Problematiques();
        $suiviProblematique = new SuiviProblematique();
        $form = $this->createForm(ProblematiquesType::class, $problematique);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()) {
                $problematique->setAuteur($this->getUser());

                $suiviProblematique->setProblematique($problematique);
    
                $manager->persist($problematique);
    
                $manager->persist($suiviProblematique);
    
                $manager->flush();
    
                $this->addFlash('success', 'La problématique a bien été ajoutée.');
    
                return $this->redirectToRoute('afficher_problematiques');
            }else {
                $this->addFlash('danger', 'La problématique n\'a pas été ajoutée.');
            }
            
        } 

        return $this->render('/pages/problematiques/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modifie une problématique
     * 
     * @param Problematiques $problematique
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('problematiques/edit/{id}', name: 'app_problematiques_edit', methods: ['GET', 'POST'])]
    public function editerProblematique(
        Problematiques $problematique,
        Request $request,
        EntityManagerInterface $entityManager,
        SuiviProblematiqueRepository $suiviProblematiqueRepository
    ): Response {
        $suiviProblematique = $suiviProblematiqueRepository->findOneBy(['problematique' => $problematique]);

        $form = $this->createForm(CombinedFormType::class, [
            'problematique' => $problematique,
            'suiviProblematique' => $suiviProblematique,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($problematique);
                $entityManager->persist($suiviProblematique);
                $entityManager->flush();

                $this->addFlash('success', 'La problématique a été modifiée avec succès.');

                return $this->redirectToRoute('afficher_problematiques');
            } else {
                $this->addFlash('danger', 'La problématique n\'a pas été modifiée.');
            }
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
    public function supprimeProblematique(
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
    public function valider(
        Problematiques $problematique,
        EntityManagerInterface $manager,
        SuiviProblematiqueRepository $suiviProblematiqueRepository
    ): Response {
        $suiviProblematique = $suiviProblematiqueRepository->findOneBy(['problematique' => $problematique]);

        $suiviProblematique->setEtat(EtatProblematiqueEnum::NON_RESOLU);
        $suiviProblematique->setMembreValidateur($this->getUser());
        $problematique->setDateModif(new \DateTimeImmutable());

        $manager->persist($suiviProblematique);
        $manager->flush();

        $this->addFlash('success', 'La problématique a bien été validée.');

        return $this->redirectToRoute('afficher_problematiques');
    }

    /**
     * Affichage d'une problematique par l'id
     * 
     * @param Problematiques $problematique
     * @return Response
     */
    #[Route('problematiques/{id}', name: 'voir_problematique', methods: ['GET'])]
    public function voir(
        Problematiques $problematique,
        SuiviProblematiqueRepository $suiviProblematiqueRepository
    ): Response {
        $suiviProblematique = $suiviProblematiqueRepository->findOneBy(['problematique' => $problematique]);

        return $this->render('pages/problematiques/problematique.html.twig', [
            'problematique' => $problematique,
            'suiviProblematique' => $suiviProblematique,
        ]);
    }
}
