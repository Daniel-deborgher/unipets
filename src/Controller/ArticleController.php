<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Sujet;
use App\Entity\Comment;
use App\Entity\EditSujet;
use App\Form\ArticleType;
use App\Form\CommentType;
use Cocur\Slugify\Slugify;
use App\Form\EditSujetType;
use App\Repository\SujetRepository;
use Symfony\Component\Mime\Address;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class ArticleController extends AbstractController
{
    /**
     * Permet de supprimer une annonce
     * 
     * @Route("/messujets/delete/{id}", name="sujet_delete")
     * 
     * @return Response
     */
    public function delete (Sujet $sujet){
        $user = $this->getUser();
        if ($user->getId() == $sujet->getAuthors()->getId() && $sujet->getDesable() == null) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($sujet);
        $entityManager->flush();

        $this->addFlash(
            'success',
            "Votre sujet a bien été supprimé !"
        );
            return $this->redirectToRoute('my_sujets');
        }
        else {
            throw new AccessDeniedException();
        }
    }

    /**
     * @Route("/add/sujet", name="add_sujet")
     * @IsGranted("ROLE_USER")
     */
    public function sujet(Request $request, TokenGeneratorInterface $tokenGenerator): Response
    {
        
        $entityManager = $this->getDoctrine()->getManager();
        $contact = new Sujet();
        $form = $this->createForm(ArticleType::class, $contact);
        $form->handleRequest($request);
        $slugify = new Slugify();
        
            
        if($form->isSubmitted() && $form->isValid()){
            if(!$contact->getId()){
                $contact->setCreatedAt(new \DateTime()); 
             }
             
        $contact->setAutheur($this->getUser()->getFirstName());
        $contact->setAuteur($this->getUser()->getLastName());
        $contact->setAuthors($this->getUser());
            $fichier = $form->get("image")->getData();
            if($fichier){
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nomFichier .= "." . $fichier->guessExtension();
                $nomFichier = str_replace(" ", "_", $nomFichier);
                //on enregistre le fichier téléchargé dans un dossier public/images
                $dossier = $this->getParameter("dossier_images");
                $fichier->move($dossier, $nomFichier);
                $contact->setImage($nomFichier);
                
            }
            $slug = $slugify->slugify($contact->getTitre());
            $contact->setSlug($slug);
            if ($contact->getAverti() == true) {
                $registrationToken = $tokenGenerator->generateToken();
                $contact->setDesable($registrationToken);
            }
            
            $entityManager->persist($contact);
            $entityManager->flush();
            
            
          
            $this->addFlash("success", "Votre sujet a bien été ajouté ! ");

            return $this->redirectToRoute("show_sujet", [
                'titrecategory' => $contact->getCategory()->getSlug(),
                'slug' => $contact->getSlug()
            ]);
          
        }
        return $this->render('sujet/add.sujet.html.twig', [           
            'form' => $form->createView(),
            
        ]);
    }
    /**
     * @Route("/sujets", name="sujets")
     */
    public function sujets(Request $request, PaginatorInterface $paginator): Response
    {
        $donnees = $this->getDoctrine()->getRepository(Sujet::class)->findBy([],['id' => 'desc']);

        $sujets = $paginator->paginate(
            $donnees, // on lui passe nos données
            $request->query->getInt('page', 1), // Numéro de la page en cours, 1 par défaut
            3
        );
        return $this->render('sujet/sujet.html.twig', [
            'sujets' => $sujets
        ]);
    }
    /**
     * Permet d'afficher un sujet
     * 
     * @Route ("/sujet/{titrecategory}/{slug}", name="show_sujet")
     * 
     */
    public function show_sujet($slug, SujetRepository $repo, Sujet $sujet, Request $request, MailerInterface $mailer) {
    $entityManager = $this->getDoctrine()->getManager();
    $user = $this->getUser();

    $comment = new Comment;

    $form = $this->createform(CommentType::class, $comment);

    $form->handleRequest($request);

    if($form->isSubmitted() && $form->isValid()){
        
        

        $comment->setCreatedAt(new \DateTime())
                ->setSujet($sujet)
                ->setAuthor($this->getUser());
        
        $entityManager->persist($comment);
        $chaine1 = $comment->getContent();
        $chaine2 = 'delete';
        $chaine3 = 'DELETE';
        $chaine4 = 'truncate';
        $chaine5 = 'TRUNCATE';
        $chaine6 = 'select';
        $chaine7 = 'SELECT';

        if(strstr($chaine1, $chaine2)) {
            $this->addFlash(
                'success',
                'Votre commentaire a bien été pris en compte !'
            );
        }

        elseif(strstr($chaine1, $chaine3)){
            $this->addFlash(
                'success',
                'Votre commentaire a bien été pris en compte !'
            );
        }

        elseif(strstr($chaine1, $chaine4)){
            $this->addFlash(
                'success',
                'Votre commentaire a bien été pris en compte !'
            );
        }

        elseif(strstr($chaine1, $chaine5)){
            $this->addFlash(
                'success',
                'Votre commentaire a bien été pris en compte !'
            );
        }

        elseif(strstr($chaine1, $chaine6)){
            $this->addFlash(
                'success',
                'Votre commentaire a bien été pris en compte !'
            );
        }

        elseif(strstr($chaine1, $chaine7)){
            $this->addFlash(
                'success',
                'Votre commentaire a bien été pris en compte !'
            );
        }

        else {
            
             $entityManager->flush();
             if ($sujet->getAuthors()->getId() == $comment->getAuthor()->getId()) {
                //rien ...
             }
             else {
                if ($sujet->getAverti() == true) {
                    $registrationToken = $sujet->getDesable();
                    $email = (new TemplatedEmail())
                    ->from('contact.unipets@gmail.com')
                    ->to(new Address($sujet->getAuthors()->getEmail()))
                    ->subject('Une réponse à votre sujet a été posté')
                    ->htmlTemplate('sujet/send_email.html.twig')
                    ->context([
                        'comment' => $comment,
                        'sujet'=> $sujet,
                        'sujetID' => $sujet->getId(),
                        'registrationToken'=> $registrationToken
                    ]);
        
                    $mailer->send($email);
            }
             }
             
             $this->addFlash(
            'success',
            'Votre commentaire a bien été pris en compte !'
        );
        }
        
    }
    // Je récupère l'article qui correspond au slug
    $sujet = $repo->findOneBySlug($slug);
    return $this->render('sujet/show.html.twig', [
        'sujet' => $sujet,
        'commentForm' => $form->createView(),
        'comments' =>$comment
    ]);
}
    /**
     * Permet d'afficher un sujet
     * 
     * @Route ("/mes-sujets", name="my_sujets")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function my_sujetsRequest (Request $request, PaginatorInterface $paginator) {
        $user = $this->getUser();
        $donnees = $this->getDoctrine()->getRepository(Sujet::class)->findBy(['Authors' => $user->getId()],['createdAt' => 'desc']);

        $sujets = $paginator->paginate(
            $donnees, // on lui passe nos données
            $request->query->getInt('page', 1), // Numéro de la page en cours, 1 par défaut
            10
        );

        return $this->render('sujet/mysujets.html.twig', [
            'sujets' => $sujets
        ]);

        
    }
    /**
     * Permet de se désabonner depuis les mails
     * 
     * @Route ("/se-d-abonner/{id<\d+>}/{token}", name="desabonner", methods={"GET"})
     * @return Response
     */
    public function desabonner (Sujet $sujet, $token) {
        $entityManager = $this->getDoctrine()->getManager();

        if(($sujet->getDesable() === null) || ($sujet->getDesable()!== $token)){
            throw new AccessDeniedException();
        }
        $sujet->setAverti(null);
        $sujet->setDesable(null);
        $entityManager->flush();

        $this->addFlash(
            'success', 'Vous ne recevrez plus désormais de notification pour ce sujet.'
        );
        return $this->redirectToRoute('home');

        
    }
    /**
     * Permet de se désabonner depuis mes sujets
     * 
     * @Route ("/stop/{id<\d+>}/{token}", name="stopabonner", methods={"GET"})
     * @return Response
     */
    public function stopabonner (Sujet $sujet, $token) {
        $entityManager = $this->getDoctrine()->getManager();

        if(($sujet->getDesable() === null) || ($sujet->getDesable()!== $token)){
            throw new AccessDeniedException();
        }
        $sujet->setAverti(null);
        $sujet->setDesable(null);
        $entityManager->flush();

        $this->addFlash(
            'success', 'Vous ne recevrez plus désormais de notification pour ce sujet.'
        );
        return $this->redirectToRoute('my_sujets');

        
    }
        /**
     * Permet de se désabonner depuis le site
     * 
     * @Route ("/{id<\d+>}/{token}", name="desabonnement", methods={"GET"})
     * @return Response
     */
    public function desabonnement (Sujet $sujet, $token) {
        $entityManager = $this->getDoctrine()->getManager();

        if(($sujet->getDesable() === null) || ($sujet->getDesable()!== $token)){
            throw new AccessDeniedException();
        }
        $sujet->setAverti(null);
        $sujet->setDesable(null);
        $entityManager->flush();

        $this->addFlash(
            'success', 'Vous ne recevrez plus désormais de notification pour ce sujet.'
        );
        return $this->redirectToRoute('show_sujet', ['titrecategory'=> $sujet->getCategory()->getSlug() , 'slug' => $sujet->getSlug()]);

        
    }
    /**
     * Permet de se s'abonner mes sujets
     * 
     * @Route ("/s-abonner/{id<\d+>}", name="abonner", methods={"GET"})
     * @return Response
     */
    public function abonner (Sujet $sujet, TokenGeneratorInterface $tokenGenerator) {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($user->getId() == $sujet->getAuthors()->getId() && $sujet->getDesable() == null) {
            $sujet->setAverti(true);
            $registrationToken = $tokenGenerator->generateToken();
            $sujet->setDesable($registrationToken);
            $entityManager->flush();

        $this->addFlash(
            'success', 'Vous recevrez désormais les réponses des autres utilisateurs pour ce sujet.'
        );
        return $this->redirectToRoute('my_sujets');

    }
    else {
        throw new AccessDeniedException();
    }
    }
    /**
     * Permet de se s'abonner le sujet
     * 
     * @Route ("/sabonner/{id<\d+>}", name="sabonner", methods={"GET"})
     * @return Response
     */
    public function sabonner (Sujet $sujet, TokenGeneratorInterface $tokenGenerator) {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($user->getId() == $sujet->getAuthors()->getId() && $sujet->getDesable() == null) {
            $sujet->setAverti(true);
            $registrationToken = $tokenGenerator->generateToken();
            $sujet->setDesable($registrationToken);
            $entityManager->flush();

        $this->addFlash(
            'success', 'Vous recevrez désormais les réponses des autres utilisateurs pour ce sujet.'
        );
        return $this->redirectToRoute('show_sujet', ['titrecategory'=> $sujet->getCategory()->getSlug() ,'slug' => $sujet->getSlug()]);

    }
    else {
        throw new AccessDeniedException();
    }
    }
    /**
     * Permet d'afficher et de de traiter le formulaire de modification du profil
     * 
     * @Route("/modifier/{slug}", name="edit_sujet")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
     public function editsujet(Sujet $sujet, Request $request){
        $user = $this->getUser();
        if ($user->getId() == $sujet->getAuthors()->getId() && $sujet->getDesable() == null) {
            $entityManager = $this->getDoctrine()->getManager();
            $donnes = new EditSujet();
           
                $form =$this->createForm(EditSujetType::class, $donnes);
                $form->handleRequest($request);
                $titre = $form->get("titre")->getData();
                $contenu = $form->get("contenu")->getData();
                if($form->isSubmitted() && $form->isValid()){
                    $sujet->setTitre($titre);
                    $sujet->setContenu($contenu);
                    $entityManager->persist($sujet);
                    $entityManager->flush();
                    $this->addFlash(
                        'success', 'Les données du profil ont été mis à jour avec succès !'
                    );
                    return $this->redirectToRoute("home");

                }
            return $this->render('sujet/edit.sujet.html.twig',
        ['form' => $form->createView(),
            'sujet' => $sujet]);
        }

            
            else {
                throw new AccessDeniedException();
            }
        }
    
}
