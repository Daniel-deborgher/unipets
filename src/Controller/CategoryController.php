<?php

namespace App\Controller;

use App\Entity\Sujet;
use App\Entity\Category;
use App\Form\CategoryType;
use Cocur\Slugify\Slugify;
use App\Repository\SujetRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
     /**
     * @Route("/category", name="add_produit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function category(Request $request): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $contact = new Category();
        $form = $this->createForm(CategoryType::class, $contact);
        $form->handleRequest($request);
        $slugify = new Slugify();
        
            
        if($form->isSubmitted() && $form->isValid()){
            $slug = $slugify->slugify($contact->getTitre());
            $contact->setSlug($slug);
           
           $entityManager->persist($contact);
            $entityManager->flush();
            
            
          
            $this->addFlash("success", "Votre catégorie a bien été ajouté  ! ");

            return $this->redirectToRoute("home");
          
        }
        return $this->render('category/index.html.twig', [           
            'form' => $form->createView(),
            
        ]);
    }
    /**
     * Permet de voir les articles dans la categorie reptiles
     * 
     * @Route("/reptiles", name="show_reptiles")
     * @return Response
     */
    public function show_category_reptiles(Request $request, PaginatorInterface $paginator)
    {
        $donnees = $this->getDoctrine()->getRepository(Sujet::class)->findBy(['category' => 3],['createdAt' => 'desc']);

        $sujets = $paginator->paginate(
            $donnees, // on lui passe nos données
            $request->query->getInt('page', 1), // Numéro de la page en cours, 1 par défaut
            10
        );

        return $this->render('category/view.reptiles.html.twig', [
            'category' => $sujets
        ]);
        
    }
    /**
     * Permet de voir les articles dans la categorie rongeurs
     * 
     * @Route("/rongeurs", name="show_rongeurs")
     * @return Response
     */
    public function show_category_rongeurs(Request $request, PaginatorInterface $paginator)
    {
        $donnees = $this->getDoctrine()->getRepository(Sujet::class)->findBy(['category' => 5],['createdAt' => 'desc']);

        $sujets = $paginator->paginate(
            $donnees, // on lui passe nos données
            $request->query->getInt('page', 1), // Numéro de la page en cours, 1 par défaut
            10
        );

        return $this->render('category/view.rongeurs.html.twig', [
            'category' => $sujets
        ]);
    }
    /**
     * Permet de voir les articles dans la categorie oiseaux
     * 
     * @Route("/oiseaux", name="show_oiseaux")
     * @return Response
     */
    public function show_category_oiseaux(Request $request, PaginatorInterface $paginator)
    {
        $donnees = $this->getDoctrine()->getRepository(Sujet::class)->findBy(['category' => 4],['createdAt' => 'desc']);

        $sujets = $paginator->paginate(
            $donnees, // on lui passe nos données
            $request->query->getInt('page', 1), // Numéro de la page en cours, 1 par défaut
            10
        );

        return $this->render('category/view.oiseaux.html.twig', [
            'category' => $sujets
        ]);
    }
}
