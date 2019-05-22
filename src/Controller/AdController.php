<?php

namespace App\Controller;
use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Service\Pagination;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * permet d'afficher une liste d'annonce. avec pagination
     * @Route("/ads/{page<\d+>?1}", name="ads_list")
     */
    public function index(AdRepository $repo,$page,Pagination $paginationService){

        $paginationService->setEntityClass(Ad::class)
                          ->setLimit(9)
                          ->setPage($page)
                          ;
        
     return $this->render('ad/index.html.twig', [
        'controller_name' => 'Nos Annonces',
        'pagination'=>$paginationService

        ]);
    }
    
    /**
     * Permet de créer une annone
     * @Route ("/ads/new",name="ads_create")
     * @IsGranted("ROLE_USER")
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
            $ad->setAuthor($this->getUser());
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
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()",message="Vous ne pouvez pas modifier cette annonce.")
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

    /**
     * Permet le suppression d'une annonce
     * @Route("/ads/{slug}/delete",name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()",message="Vous n'avez pas le droit d'accepter a cette page")
     *
     * @param Ad $ad
     * @return void
     */
    public function delete(Ad $ad,ObjectManager $manager){

        $manager->remove($ad);
        $manager->flush();
        $this->addFlash("success","L'annonce <em>{$ad->getTitle()}</em> a bien été supprimé !");

        return $this->redirectToRoute("ads_list");

    }

}
