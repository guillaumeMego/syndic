<?php 

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class GroupePageController extends AbstractController
{
    #[Route('/grouped-page', name: 'groupe_page', methods: ['GET'])]
    #[IsGranted('ROLE_LOCATAIRE')]
    public function index(): Response
    {
        return $this->render('pages/groupe.html.twig');
    }
}
