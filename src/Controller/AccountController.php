<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\EditType;
use App\Form\ForgotPasswordType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Service\SendEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface as Password;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'hasError' => $error !== null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se déconnecter
     *
     * @Route ("/logout", name="account_logout")
     * @return void
     */
    public function logout(){
        //rien
    }

    /**
     * permet d'afficher le formulaire d'inscription
     *
     * @Route ("/inscription", name="account_register")
     * @return Response
     */
    public function register(Request $request, Password $encoder, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer){
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $fichier = $form->get("picture")->getData();
            if($fichier){
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nomFichier .= uniqid();
                $nomFichier .= "." . $fichier->guessExtension();
                $nomFichier = str_replace(" ", "_", $nomFichier);
                //on enregistre le fichier téléchargé dans un dossier public/images
                $dossier = $this->getParameter("dossier_images");
                $fichier->move($dossier, $nomFichier);
                $user->setPicture($nomFichier);
            }
          $registrationToken = $tokenGenerator->generateToken();

          $user->setRegistrationToken($registrationToken);

          $hash = $encoder->encodePassword($user, $user->getHash());
          $user->setHash($hash);

          $entityManager->persist($user);
          
          $entityManager->flush(); 

          $email = (new TemplatedEmail())
            ->from('contact.unipets@gmail.com')
            ->to(new Address($user->getEmail()))
            ->subject('Vérification de votre adresse e-mail pour activer votre compte')
            ->htmlTemplate('account/registrer_confirmation_email.html.twig')
            ->context([
                'userID' => $user->getId(),
                'registrationToken' => $registrationToken,
            ]);

            $mailer->send($email);
          
          $this->addFlash("success", "Votre compte a bien été créé, vérifiez vos e-mails pour l'activer !");

          return $this->redirectToRoute("account_login");
          
        }
        

        return $this->render('account/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    
    /**
     * Permet de modifier le mot de passe 
     * 
     * @Route("/mon-profile/modification-mot-de-passe", name="account_updatePassword")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updatePassword(Request $request, Password $encoder){
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate(); 

        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
             // Vérifier que le oldPassword du formulaire soit le même que le password de l'user
             
             if (!password_verify($passwordUpdate->getOldPassword(), $user->getHash())) {
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez saisi ne correspond pas à votre mot de passe actuel"));
             }
             else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );
                return $this->redirectToRoute('home');
             }
        }

        return $this->render('account/password.html.twig',
    ['form' => $form->createView()]);
    }

    /**
     * @Route("/mot-de-passe-oublie", name="account_oublie", methods={"GET","POST"})
     */
    public function oublie(Request $request, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailerInterface, UserRepository $repo)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $form= $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $repo->findOneBy(
                ['email' => $form['email']->getData()
            ]);
            /* Nous faisons un leurre au cas où il n'y pas d'utilisateur */

            if(!$user){ 
                $this->addFlash('danger', "L'utilisateur n'existe pas");
                return $this->redirectToRoute('account_login');
            }

            $user->setForgotPasswordToken($tokenGenerator->generateToken());

            $entityManager->flush();

            $email = (new TemplatedEmail())
            ->from('contact.unipets@tansaku.com')
            ->to(new Address($user->getEmail()))
            ->subject('Modification de votre mot de passe')
            ->htmlTemplate('forgot_password/forgot_password_email.html.twig')
            ->context([
                'user' => $user,
            ]);

            $mailerInterface->send($email);

            $this->addFlash('success', "Un e-mail de réinitialisation de mot de passe a été envoyé");
            return $this->redirectToRoute('account_login');
        }

        return $this->render('forgot_password/forgot_password_step_1.html.twig', [
            'forgotPasswordFormStep1' => $form->createView(),
        ]);
    }
}
