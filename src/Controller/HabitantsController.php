<?php

namespace App\Controller;

use App\Entity\Habitants;
use App\Form\HabitantsType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\HabitantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HabitantsController extends AbstractController
{
    /**
     * Cette fonction permet d'afficher la liste des habitants
     *
     * @param HabitantsRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/habitants', name: 'app_habitants', methods: ['GET'])]
    public function index(HabitantsRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $habitants = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10 
        );
    
        return $this->render('pages/habitants/index.html.twig', [
            'habitants' => $habitants,
        ]);
    }
    
    /**
     * Cette fonction permet d'afficher le formulaire d'ajout d'un habitant
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/habitants/new', name: 'app_habitants_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
        ): Response
    {
        $habitants = new Habitants();
        $form = $this->createForm(HabitantsType::class, $habitants);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //ajout habitant et mdp
            $habitants->setMdp(password_hash($habitants->getMdp(), PASSWORD_DEFAULT));
            $manager->persist($habitants);
            $manager->flush();
        
            $this->addFlash('success', 'L\'habitant a bien été ajouté');
            return $this->redirectToRoute('app_habitants');
        }


        return $this->render('pages/habitants/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Cette fonction permet d'afficher le formulaire d'édition d'un habitant
     *
     * @param Habitants $habitants
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route ('/habitants/edit/{id}', name: 'app_habitants_edit', methods: ['GET', 'POST'])]
    public function edit(Habitants $habitants, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(HabitantsType::class, $habitants);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //ajout habitant et mdp
            $habitants->setMdp(password_hash($habitants->getMdp(), PASSWORD_DEFAULT));
            $manager->persist($habitants);
            $manager->flush();
        
            $this->addFlash('success', 'L\'habitant a bien été modifié');
            return $this->redirectToRoute('app_habitants');
        }

        return $this->render('pages/habitants/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
