<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditType;
use App\Form\UserType;
use App\Entity\EditUpdate;
use App\Entity\EditPicture;
use App\Form\EditPictureType;
use App\Entity\PasswordUpdate;
use App\Form\ForgotPasswordType;
use App\Form\PasswordUpdateType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface as Password;

class UserController extends AbstractController
{
    
    /**
     * @Route("/login", name="account_login")
     */
    
    public function login(AuthenticationUtils $utils): Response
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
     * @Route("/register", name="account_register")
     * 
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User;

        $form = $this ->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if( $form->isSubmitted() && $form->isValid()){
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
            ->from('contacter@tansaku.com')
            ->to(new Address($user->getEmail()))
            ->subject('Vérification de l\'adresse e-mail d\'un nouvel utilisateur')
            ->htmlTemplate('user/registrer_confirmation_email.html.twig')
            ->context([
                'user' => $user,
                'userID' => $user->getId(),
                'registrationToken' => $registrationToken,
            ]);

            $mailer->send($email);
          
          $this->addFlash("success", "Votre compte a bien été créé, vérifiez vos e-mails pour l'activer !");
          
            return $this->redirectToRoute("account_login");
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route ("/verifier/{id<\d+>}/{token}", name="app_verify_account", methods={"GET"})
     * @return Response
     */
    public function verifyUserAccount(User $user, $token, MailerInterface $mailer){
        $entityManager = $this->getDoctrine()->getManager();

        if(($user->getRegistrationToken() === null) || ($user->getRegistrationToken()!== $token)){
            throw new AccessDeniedException();
        }
        $user->setIsverified(true);
        $user->setRegistrationToken(null);
        $entityManager->flush();

        $email = (new TemplatedEmail())
            ->from('contact.unipets@gmail.com')
            ->to(new Address($user->getEmail()))
            ->subject('Votre compte est désormais actif !')
            ->htmlTemplate('account/confirmation_email.html.twig')
            ->context([
                'user' => $user,
            ]);

            $mailer->send($email);

        $this->addFlash(
            'success', 'Votre compte a bien été vérifié, vous pouvez dès à présent vous connecter.'
        );
        return $this->redirectToRoute('account_login');
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
        ->from('contact.tansaku@tansaku.com')
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
public function __construct(UserRepository $userRepository, SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->session       = $session;
        $this->userRepository= $userRepository;
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
     * Permet d'afficher et de de traiter le formulaire de modification du profil
     * 
     * @Route("/mon-profil/modification-des-donnees", name="edit_profile")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */

    public function editprofile(Request $request){
        $entityManager = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $donnes = new EditUpdate();
           
                $form =$this->createForm(EditType::class, $donnes);
                $form->handleRequest($request);
                $fistname = $form->get("firstName")->getData();
                $lastname = $form->get("lastName")->getData();
                if($form->isSubmitted() && $form->isValid()){
                    $user->setFirstname($fistname);
                    $user->setLastname($lastname);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $this->addFlash(
                        'success', 'Les données du profil ont été mis à jour avec succès !'
                    );
                    return $this->redirectToRoute("home");

                }
            return $this->render('account/edit.profile.html.twig',
        ['form' => $form->createView()]);
        }
        /**
     * Permet d'afficher le formulaire d'édition
     *
     * @Route ("/mon-profile/modification-mot-de-l-avatar", name="edit_avatar")
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function edit_avatar(Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $picture = new EditPicture();
        if ($user->getPicture()) {
            $user->setPicture(
                new File('images' . '/' . $user->getPicture())
            );
        } else {
            # code...
        }
        
        

        $form = $this->createForm(EditPictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           
            $fichier = $form->get("picture")->getData();
            if($fichier){

                $oldFile1 = $user->getPicture();
                if ($oldFile1) {
                    unlink($oldFile1);
                }
                $nomFichier = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                $nomFichier .= "." . $fichier->guessExtension();
                $nomFichier = str_replace(" ", "_", $nomFichier);
                //on enregistre le fichier téléchargé dans un dossier public/images
                $dossier = $this->getParameter("dossier_images");
                $fichier->move($dossier, $nomFichier);
                $user->setPicture($nomFichier);
                
            }
            
           $entityManager->persist($user);
           $entityManager->flush();

           $this->addFlash(
               'success',
               "Votre avatar a bien été modifié !"
           );
           return $this->redirectToRoute("home");
        }
        return $this-> render('account/edit.picture.html.twig', [
            'form' => $form->createView()
        ]);
}


    /**
     * @Route("/forgot-password", name="app_forgot_password", methods={"GET","POST"})
     */
    public function sendRecoveryLink(Request $request, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator): Response
    {
        $form= $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy(
                ['email' => $form['email']->getData()
            ]);

            /* Nous faisons un leurre au cas où il n'y pas d'utilisateur */

            if(!$user){ 
                $this->addFlash('danger', "L'utilisateur n'existe pas");
                return $this->redirectToRoute('account_login');
            }

            $user->setForgotPasswordToken($tokenGenerator->generateToken());

            $this->entityManager->flush();

            $email = (new TemplatedEmail())
                ->to(new Address($user->getEmail()))
                ->subject("Modification de votre mot de passe")
                ->htmlTemplate('forgot_password/forgot_password_email.html.twig')
                ->context([
                    'user' => $user
                ]);
                $mailer->send($email);
            $this->addFlash('success', "Un e-mail de réinitialisation de mot de passe a été envoyé");
            return $this->redirectToRoute('account_login');
        }

        return $this->render('forgot_password/forgot_password_step_1.html.twig', [
            'forgotPasswordFormStep1' => $form->createView(),
        ]);
    }
    /**
     * @Route("/forgot-password/{id<\d+>}/{token}", name="app_retrieve_credentials", methods={"GET"})
     *
     */
    public function retrieveCredentialsFromTheUrl($token, User $user): RedirectResponse
    {
        $this->session->set('Reset-Password-Token-URL', $token);

        $this->session->set('Reset-Password-User-Email', $user->getEmail());

        return $this->redirectToRoute('app_reset_password');
    }

    /**
     *
     * @Route("/reset-password", name="app_reset_password", methods={"GET","POST"})
     */

    public function resetPasswword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        [
            'token'=> $token,
            'userEmail' => $userEmail
        ]= $this->getCredentialsFromSession();
        $passwordUpdate = new PasswordUpdate(); 

        $user = $this->userRepository->findOneBy(['email' => $userEmail ]);
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        if (!$user) {
            return $this->redirectToRoute('app_forgot_password');
        }
        /** Si le token de l'utilisateur ou si le token passé dans l'url est différent de son token en base de donnée, alors on le renvoie vers app_forgot_password */
        if (($user->getForgotPasswordToken() === null) || $user->getForgotPasswordToken()!== $token) {
            return $this->redirectToRoute('app_forgot_password');
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            
             $user->setHash($encoder->encodePassword($user, $form['newPassword']->getData()));

            /** On rend le token inutilisable */
            $user->setForgotPasswordToken(null);
            $this->entityManager->flush();

            $this->removeCredentialsFromSession();

            $this->addFlash('success', 'Votre mot de passe a été modifié, vous pouvez dès à présent vous connecter !');

            return $this->redirectToRoute('account_login');
        }
        return $this->render('forgot_password/forgot_password_step_2.html.twig', [
            'forgotPasswordFormStep2' => $form->createView()
        ]);
    }
    private function getCredentialsFromSession(): array
    {
        return [
            'token' => $this->session->get('Reset-Password-Token-URL'),
            'userEmail' =>$this->session->get('Reset-Password-User-Email')
        ];
    }
    private function removeCredentialsFromSession(): void
    {
        $this->session->remove('Reset-Password-Token-URL');

        $this->session->remove('Reset-Password-User-Email');
    }
}
