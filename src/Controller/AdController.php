<?php

namespace App\Controller;
use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * permet d'afficher une liste d'annonce.
     * @Route("/ads", name="ads_list")
     */
    public function index(AdRepository $repo){

        // via $repo on va chercher toutes les annonces via la methode findAll

     $ads = $repo->findAll();
        
     return $this->render('ad/index.html.twig', [
        'controller_name' => 'Nos Annonces',
        'ads'=>$ads
        ]);
    }
    
    /**
     * Permet de créer une annone
     * @Route ("/ads/new",name="ads_create")
     * @return response
     */
    
    public function create(Request $request,ObjectManager $manager){

        // formbuilder -> fabriquant de formulaire

        $ad = new Ad();




        // on lance la fabrication et la configuration du formulaire

        $form = $this->createForm(AnnonceType::class,$ad);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){


            // si le formulaire est soumis et le formulaire est validé on demande a doctrine de sauvegarder
            // les données dans l'objet $manager

            // pour chaque images supplémentaires ajoutées

            foreach($ad->getImages() as $image){

                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success',"Annonce <strong>{$ad->getTitle()}</strong> crée avec succés");

            return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
        }
    
            return $this->render('ad/new.html.twig',['form'=>$form->createView()]);
    }
        
    /**
     * permet d'affciher une seule annonce
     * @Route("/ads/{slug}", name="ads_single")
     * 
     * @return Response
     * 
     */

    public function show($slug,Ad $ad){
         // recupération de l'annonce qui correspond au slug (a l'alias)

         // $ad = $repo->findOneBySlug($slug);
         return $this->render('ad/show.html.twig',['ad'=>$ad]);

    }

    /**
     * Permet d'editer et de modifier un article
     * @Route("/ads/{slug}/edit",name="ads_edit")
     * 
     * @return response
     */

    public function edit(Ad $ad,Request $request,ObjectManager $manager){

        $form = $this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($ad->getImages() as $image){
                $image -> setAd($ad);

                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash("success","Modifications éffectuées !");

            return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
        }

        return $this->render('ad/edit.html.twig',['form'=>$form->createView(),'ad'=>$ad]);
    }

}
