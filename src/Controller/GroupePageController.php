<?php

namespace App\Controller;

use Doctrine\ORM\Query;
use CalendarBundle\Entity\Event;
use App\Repository\UserRepository;
use App\Controller\GraphiqueController;
use CalendarBundle\Event\CalendarEvent;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProblematiquesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SuiviProblematiqueRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




class GroupePageController extends AbstractController
{

    #[Route('/calendar-events', name: 'calendar_events', methods: ['GET'])]
    public function calendarEvents(EntityManagerInterface $manager): Response
    {
        // Créez une nouvelle instance de CalendarEvent
        $calendar = new CalendarEvent(new \DateTime(), new \DateTime('+1 month'), []);

        // calendar 
        $problematiques = $manager->getRepository(\App\Entity\SuiviProblematique::class)
            ->createQueryBuilder('s')
            ->leftJoin('s.problematique', 'p')
            ->select('p.id', 'p.problematique', 'p.date_ajout')
            ->where('s.etat != :etat')
            ->setParameter('etat', 'En attente de validation')
            ->getQuery()
            ->getResult();


        $calendarEvents = [];
        foreach ($problematiques as $problematique) {
            // Créez un nouvel événement CalendarBundle\Entity\Event et ajoutez-le au calendrier
            $calendarEvent = new Event(
                $problematique['problematique'],
                $problematique['date_ajout']
            );

            $calendar->addEvent($calendarEvent);

            // Convertissez l'objet Event en un tableau
            $calendarEventArray = [
                'id' => $problematique['id'],
                'title' => $calendarEvent->getTitle(),
                'start' => $calendarEvent->getStart()->format('Y-m-d H:i:s'),
                // Ajoutez tous les autres champs que vous voulez inclure dans le JSON
            ];

            $calendarEvents[] = $calendarEventArray;
        }

        return new JsonResponse($calendarEvents);
    }


    #[Route('/dashboard', name: 'dashboard', methods: ['GET'])]
    #[IsGranted('ROLE_LOCATAIRE')]
    public function index(
        UserRepository $repository,
        ProblematiquesRepository $problematiquesRepository,
        SuiviProblematiqueRepository $suiviProblematiqueRepository,
        GraphiqueController $graphiqueController,
    ): Response {
        $graphiqueData = $graphiqueController->problematiqueParAnnee($problematiquesRepository);

        $user = $this->getUser();

        $problematiques = $problematiquesRepository->findAll();
        $countProblematiques = count($problematiques);

        $problematiquesEnAttente = $suiviProblematiqueRepository->findBy(['etat' => 'En attente de validation']);
        $countSuiviEnAttente = count($problematiquesEnAttente);

        $countLocataire = $repository->countLocataire();
        $countProprietaire = $repository->countProprietaire();
        $countAdmin = $repository->countMembreConseil();

        $users = $repository->findAll();
        $count = count($users);

        return $this->render('pages/dashboard.html.twig', array_merge([
            'count' => $count,
            'countProblematiques' => $countProblematiques,
            'countSuiviEnAttente' => $countSuiviEnAttente,
            'countLocataire' => $countLocataire,
            'countProprietaire' => $countProprietaire,
            'countAdmin' => $countAdmin,
            'user' => $user
        ], $graphiqueData));
    }
}
