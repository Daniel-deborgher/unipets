<?php

namespace App\Controller;


use App\Repository\SujetRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RechercheController extends AbstractController
{
    /**
     * @Route("/recherche", name="recherche")
     */
    public function index(SujetRepository $ar, Request $rq,Request $request, PaginatorInterface $paginator)
    {
        $globaleGet = $rq->query; # contient les valeurs de $_GET
        $mot = $globaleGet->get("recherche"); # recherche est le name de mon formulaire
        $donnees = $ar->findByWord($mot);
        $sujets = $paginator->paginate(
            $donnees, // on lui passe nos données
            $request->query->getInt('page', 1), // Numéro de la page en cours, 1 par défaut
            15
        );
        return $this->render('search/index.html.twig',compact("sujets", "mot"));
    }
}