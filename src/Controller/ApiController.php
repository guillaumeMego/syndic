<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ProblematiquesRepository;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SuiviProblematiqueRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApiController extends AbstractController
{
    /**
     * API pour les problématiques
     *
     * @param ProblematiquesRepository $problematiquesRepository
     * @return JsonResponse
     */
    #[Route('/api/problematiques', name: 'api_problematiques', methods: ['GET'])]
    #[IsGranted('ROLE_CONSEIL')]
    public function problematiqueApi(
        ProblematiquesRepository $problematiquesRepository,
    ): JsonResponse {
        $problematiques = $problematiquesRepository->findAll();
        $data = [];
        foreach ($problematiques as $problematique) {
            $data[] = [
                'id' => $problematique->getId(),
                'problematique' => $problematique->getProblematique(),
                'description' => $problematique->getDescription(),
                'date d\'ajout' => $problematique->getDateAjout(),
                'date de modification' => $problematique->getDateModif(),
                'image' => $problematique->getImageName(),
                'commentaire' => $problematique->getCommentaire(),
                'auteur' => [
                    'id' => $problematique->getAuteur()->getId(),
                    'nom' => $problematique->getAuteur()->getNom(),
                    'prenom' => $problematique->getAuteur()->getPrenom(),
                    'email' => $problematique->getAuteur()->getEmail(),
                    'role' => $problematique->getAuteur()->getRoles(),
                    'lien' => [
                        'self' => '/api/residents/' . $problematique->getAuteur()->getId(),
                    ],
                ],
                'suivi problematique' => [
                    'id' => $problematique->getSuiviProblematiques()->getId(),
                    'etat' => $problematique->getSuiviProblematiques()->getEtat(),
                    'date modification' => $problematique->getSuiviProblematiques()->getDateModif(),
                    'Membre validateur' => [
                        'id' => $problematique->getSuiviProblematiques()->getMembreValidateur()->getId(),
                        'nom' => $problematique->getSuiviProblematiques()->getMembreValidateur()->getNom(),
                        'prenom' => $problematique->getSuiviProblematiques()->getMembreValidateur()->getPrenom(),
                        'email' => $problematique->getSuiviProblematiques()->getMembreValidateur()->getEmail(),
                        'role' => $problematique->getSuiviProblematiques()->getMembreValidateur()->getRoles(),
                    ],
                    'lien' => [
                        'self' => '/api/suivi_problematiques/' . $problematique->getSuiviProblematiques()->getId(),
                    ],
                ]
            ];
        }
        $response = new JsonResponse($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * API pour les problématiques par id
     * 
     * @param ProblematiquesRepository $problematiquesRepository
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/problematiques/{id}', name: 'api_problematique', methods: ['GET'])]
    #[IsGranted('ROLE_CONSEIL')]
    public function problematiqueApiId(
        ProblematiquesRepository $problematiquesRepository,
        $id
    ): JsonResponse {
        $problematique = $problematiquesRepository->find($id);
        $data = [
            'id' => $problematique->getId(),
            'problematique' => $problematique->getProblematique(),
            'description' => $problematique->getDescription(),
            'date d\'ajout' => $problematique->getDateAjout(),
            'date de modification' => $problematique->getDateModif(),
            'image' => $problematique->getImageName(),
            'commentaire' => $problematique->getCommentaire(),
            'auteur' => [
                'id' => $problematique->getAuteur()->getId(),
                'nom' => $problematique->getAuteur()->getNom(),
                'prenom' => $problematique->getAuteur()->getPrenom(),
                'email' => $problematique->getAuteur()->getEmail(),
                'role' => $problematique->getAuteur()->getRoles(),
            ],
            'suivi problematique' => [
                'id' => $problematique->getSuiviProblematiques()->getId(),
                'etat' => $problematique->getSuiviProblematiques()->getEtat(),
                'date modification' => $problematique->getSuiviProblematiques()->getDateModif(),
                'Membre validateur' => [
                    'id' => $problematique->getSuiviProblematiques()->getMembreValidateur()->getId(),
                    'nom' => $problematique->getSuiviProblematiques()->getMembreValidateur()->getNom(),
                    'prenom' => $problematique->getSuiviProblematiques()->getMembreValidateur()->getPrenom(),
                    'email' => $problematique->getSuiviProblematiques()->getMembreValidateur()->getEmail(),
                    'role' => $problematique->getSuiviProblematiques()->getMembreValidateur()->getRoles(),
                ],
            ]
        ];
        $response = new JsonResponse($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * Api pour les résidents
     * 
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
    #[Route('/api/residents', name: 'api_residents', methods: ['GET'])]
    #[IsGranted('ROLE_CONSEIL')]
    public function residentsApi(
        UserRepository $userRepository
    ): JsonResponse {
        $residents = $userRepository->findAll();
        $data = [];
        foreach ($residents as $resident) {
            $data[] = [
                'id' => $resident->getId(),
                'nom' => $resident->getNom(),
                'prenom' => $resident->getPrenom(),
                'email' => $resident->getEmail(),
                'telephone' => $resident->getTelephone(),
                'role' => $resident->getRoles(),
                'date d\'ajout' => $resident->getDateAjout(),
                'date de modification' => $resident->getDateModif(),
                'photo' => $resident->getImageName(),
                'batiment' => $resident->getBatiment(),
                'appartement' => $resident->getNumeroAppartement(),
                'etage' => $resident->getEtage(),
            ];
        }
        $response = new JsonResponse($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * Api pour les résidents par id
     * 
     * @param UserRepository $userRepository
     * @param int $id
     * @return JsonResponse
     */
    #[Route('/api/residents/{id}', name: 'api_resident', methods: ['GET'])]
    #[IsGranted('ROLE_CONSEIL')]
    public function residentApiId(
        UserRepository $userRepository,
        $id
    ): JsonResponse {
        $resident = $userRepository->find($id);
        $data = [
            'id' => $resident->getId(),
            'nom' => $resident->getNom(),
            'prenom' => $resident->getPrenom(),
            'email' => $resident->getEmail(),
            'telephone' => $resident->getTelephone(),
            'role' => $resident->getRoles(),
            'date d\'ajout' => $resident->getDateAjout(),
            'date de modification' => $resident->getDateModif(),
            'photo' => $resident->getImageName(),
            'batiment' => $resident->getBatiment(),
            'appartement' => $resident->getNumeroAppartement(),
            'etage' => $resident->getEtage(),
        ];
        $response = new JsonResponse($data);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
