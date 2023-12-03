<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProblematiquesRepository;

class GraphiqueController extends AbstractController
{
    /**
     * Renvoie les données pour le graphique des problématiques par année
     *
     * @param ProblematiquesRepository $problematiqueRepository
     * @return void
     */
    public function problematiqueParAnnee(
        ProblematiquesRepository $problematiqueRepository,
    ) {
        $problematiques = $problematiqueRepository->findBy([], ['date_ajout' => 'ASC']);

        $annee = [];
        $nbProblematique = [];
        $nbProblematiqueResolu = [];
        $nbProblematiqueNonResolu = [];

        foreach ($problematiques as $problematique) {
            $anneeAjout = $problematique->getDateAjout()->format('Y');

            if (!isset($annee[$anneeAjout])) {
                $annee[$anneeAjout] = $anneeAjout;

                $nbProblematique[$anneeAjout] = count($problematiqueRepository->createQueryBuilder('p')
                    ->where('p.date_ajout LIKE :annee')
                    ->setParameter('annee', $anneeAjout . '%')
                    ->getQuery()
                    ->getResult());
                $nbProblematiqueResolu[$anneeAjout] = count($problematiqueRepository->createQueryBuilder('p')
                    ->join('p.suiviProblematiques', 's') // Utilisez l'association correcte
                    ->where('p.date_ajout LIKE :annee')
                    ->andWhere('s.etat IN (:etats)') // Utilisez le champ 'etat' de 'SuiviProblematique'
                    ->setParameter('annee', $anneeAjout . '%')
                    ->setParameter('etats', ['Résolu'])
                    ->getQuery()
                    ->getResult());

                $nbProblematiqueNonResolu[$anneeAjout] = count($problematiqueRepository->createQueryBuilder('p')
                    ->join('p.suiviProblematiques', 's') // Utilisez l'association correcte
                    ->where('p.date_ajout LIKE :annee')
                    ->andWhere('s.etat IN (:etats)') // Utilisez le champ 'etat' de 'SuiviProblematique'
                    ->setParameter('annee', $anneeAjout . '%')
                    ->setParameter('etats', ['Non résolu'])
                    ->getQuery()
                    ->getResult());
            }
        }

        return [
            'annee' => $annee,
            'nbProblematique' => $nbProblematique,
            'nbProblematiqueResolu' => $nbProblematiqueResolu,
            'nbProblematiqueNonResolu' => $nbProblematiqueNonResolu,
        ];
    }
}
