<?php

// namespace : chemin du controller

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// pour créer une page: 1- une fonction publique (classe) /  2- une route / 3- une réponse

class HomeController extends Controller {

    /**
     * Création de la premiere route
     * @Route("/", name="home")
     */

    public function home(){

        $nom =['Fontaine' => 'visiteur','Durand' => 'admin','Dupont' => 'contributeur'];

        return $this -> render('home.html.twig',['title' => 'Location de gîtes','acces'=>'admin','tableau' => $nom]);
    }

    /**
     * Création du route paramétrée
     * Route de la page pour saluer l'utilisateur
     * @Route("/profil/{nom}", name="hello-utilisateur")
     * @Route("/hello", name="hello-base")
     * @Route("/profil/{nom}/acces/{acces}",name="hello-profil")
     * @return void
     */

    public function hello($nom="anonyme",$acces="visiteur"){

        return $this->render('hello.html.twig',['title'=>'Page de profil','nom'=>$nom,'acces'=>$acces]);
    }
}