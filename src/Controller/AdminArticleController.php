<?php

namespace App\Controller;

use App\Entity\Sujet;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\EditSujet;
use App\Form\ArticleType;
use App\Form\EditSujetType;
use App\Repository\SujetRepository;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminArticleController extends AbstractController
{
        /**
     * @Route("/admin/sujets", name="admin_sujets_index")
     */
    public function admin_show_sujets(SujetRepository $repo): Response
    {

        return $this->render('admin/sujet/index.html.twig', [
            'sujets' => $repo->findAll(),
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route ("/editor/sujet/{slug}/edit", name="admin_sujet_edit")
     * @IsGranted("ROLE_EDITOR")
     *
     * @return Response
     */
    public function edit(Sujet $sujet, Request $request){
        $donnes = new EditSujet();
        $entityManager = $this->getDoctrine()->getManager();

        $form = $this->createForm(EditSujetType::class, $donnes);
        $form->handleRequest($request);
        $titre = $form->get("titre")->getData();
        $contenu = $form->get("contenu")->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            
            $sujet->setTitre($titre);
            $sujet->setContenu($contenu);
           $entityManager->persist($sujet);
           $entityManager->flush();

           $this->addFlash(
               'success',
               "Le sujet a bien été modifié !"
           );
           return $this->redirectToRoute('admin_sujets_index');
        }
        return $this-> render('admin/sujet/edit.html.twig', [
            'sujet' => $sujet,
            'form' => $form->createView()
        ]);
    }
    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/admin/sujet/{id}/delete", name="admin_sujet_delete")
     * 
     * @return Response
     */
    public function delete (Sujet $sujet){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sujet);
        $entityManager->flush();

        $this->addFlash(
            'success',
            "Le sujet a bien été supprimé !"
        );
            return $this->redirectToRoute('admin_sujets_index');
    }
    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/delete/rongeurs/{id}", name="delete_rongeurs")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function delete_rongeurs (Sujet $sujet){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sujet);
        $entityManager->flush();

        $this->addFlash(
            'success',
            "Le sujet a bien été supprimé !"
        );
            return $this->redirectToRoute('show_rongeurs');
    }
    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/delete/oiseaux/{id}", name="delete_oiseaux")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function delete_oiseaux (Sujet $sujet){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sujet);
        $entityManager->flush();

        $this->addFlash(
            'success',
            "Le sujet a bien été supprimé !"
        );
            return $this->redirectToRoute('show_oiseaux');
    }
     /**
     * Permet de supprimer une annonce
     * 
     * @Route("/delete/reptiles/{id}", name="delete_reptiles")
     * @IsGranted("ROLE_ADMIN")
     * @return Response
     */
    public function delete_reptiles (Sujet $sujet){
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sujet);
        $entityManager->flush();

        $this->addFlash(
            'success',
            "Le sujet a bien été supprimé !"
        );
            return $this->redirectToRoute('show_reptiles');
    }
}
