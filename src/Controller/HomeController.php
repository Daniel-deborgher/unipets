<?php

namespace App\Controller;

use App\Repository\SujetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SujetRepository $repo): Response
    {
        return $this->render('home/index.html.twig', [
            'reptiles' => $repo->findByLastCategory(3),
            'oiseaux' => $repo->findByLastCategory(4),
            'rongeurs' => $repo->findByLastCategory(5),
        ]);
    }
    /**
     * @Route("/politique-de-confidentialite", name="policity")
     */
    public function policity_privacy(): Response
    {
        return $this->render('home/en_savoir_plus.html.twig');
    }
    /**
     * @Route("/mentions-legales", name="mentions")
     */
    public function mentions_legales(): Response
    {
        return $this->render('home/mentions_legales.html.twig');
    }
}
