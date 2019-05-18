<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Affichage de la page de connexion
     * @Route("/login", name="account_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();


        return $this->render('account/login.html.twig',[
            'hasError'=>$error!==null,
            'username'=>$username
        ]);
    }

    /**
     * permet la deconnexion
     * @Route("/logout",name="account_logout")
     * @return void
     */
    public function logout(){

        // tout se passe via le fichier 'security.yaml'
    }

    /**
     * Permet de s'enregistrer
     * @Route("/register",name="account_register")
     */
    public function register(Request $request,UserPasswordEncoderInterface $encoder,ObjectManager $manager){

        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $hash = $encoder->encodePassword($user,$user->getHash());

            // on modifie le mot de passe avec le setter

            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();
            $this->addFlash("success","Votre compte a bien été crée");

            return $this->redirectToRoute("account_login");
        }

        return $this->render('account/register.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * Modification de la page profil
     * @Route("/account/profile",name="account_profile")
     * @IsGranted("ROLE_USER")
     * 
     *
     * @return Response
     */
    public function profile(Request $request,ObjectManager $manager){

        $user = $this->getUser();

        $form=$this->createForm(AccountType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();
            $this->addFlash("success","Vos informations ont été correctement modifiées");
        }


        return $this->render('account/profile.html.twig',['form'=>$form->createView()]);
    }
    /**
     * Permet la modification du mot de passe
     * @Route("/account/update-password",name="account_password")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function updatePassword(Request $request,UserPasswordEncoderInterface $encoder,ObjectManager $manager){

        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form=$this->createForm(PasswordUpdateType::class,$passwordUpdate);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // on verifie que le mot de passe actuel est correct (si il est pas le bon)
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash())){

                // message d'erreur
                // $this->addFlash("warning","Votre Actuel Mot de passe est incorrect");
                $form->get('oldPassword')->addError(new FormError("Ce Mot de passe est incorrect !"));
            }else{

                // on récupère le nouveau mot de passe
                $newPassword = $passwordUpdate->getNewPassword();
    
                // on crypte le nouveau mot de passe
                $hash = $encoder->encodePassword($user,$newPassword);
    
                // On modifie le nouveau mot de passe dans le setter
                $user->setHash($hash);
    
                // On enregistre
                $manager->persist($user);
                $manager->flush();
    
                // On ajoute un message Flash
                $this->addFlash("success","Nouveau Mot de Passe correctement enregistré");
    
                // On redirige
                return $this->redirectToRoute('account_profile');
            }

        }

        return $this->render('account/password.html.twig',['form'=>$form->createView()]);
    }

    /**
     * Affichage de la page mon compte
     * @Route("/account",name="account_home")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function myAccount(){

        return $this->render("user/index.html.twig",['user'=>$this->getUser()]);
    }

    /**
     * Affiche la liste des réservation de l'utilisateur
     * @Route("/account/bookings", name="account_bookings")
     *
     * @return Response
     */
    public function bookings(){

        return $this->render('account/bookings.html.twig');
    }
}
