<?php

// namespace : chemin du controller

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// pour créer une page: 1- une fonction publique (classe) /  2- une route / 3- une réponse

class HomeController extends AbstractController {

    /**
     * Création de la premiere route
     * @Route("/", name="home")
     */

    public function home(AdRepository $adRepo,UserRepository $userRepo){

        return $this -> render('home.html.twig',
                                ['ads'=>$adRepo->findBestAds(3),
                                'users'=>$userRepo->findBestUsers()


                                ]);
    }

}