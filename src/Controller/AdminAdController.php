<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Service\Pagination;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_list")
     */
    public function index(AdRepository $repo,$page,Pagination $paginationService)
    {

        $paginationService->setEntityClass(Ad::class)
                          ->setPage($page)
                            /* ->setRoute('admin_ads_list') */
                            ;

        return $this->render('admin/ad/index.html.twig', [
            'pagination'=>$paginationService
            
        ]);
    }

    /**
     * Permet de modifier une annonce (admin)
     * @Route("admin/ads/{id}/edit", name="admin_ads_edit")
     *
     * @param Ad $ad
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Ad $ad,Request $request,ObjectManager $manager){
        
        $form = $this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success',"l'annonce a bien été modifiée");
        }


        return $this->render('admin/ad/edit.html.twig',[
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);

    }

    /**
     * Suppression d'une annonce
     * @Route("/admin/ads/{id}/delete",name="admin_ads_delete")
     *
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad,ObjectManager $manager){

        if(count($ad->getBookings()) >0){
            $this->addFlash('warning',"Interdit de supprimer une annonce qui possède des réservations.");

        }else{

            
            $manager->remove($ad);
            $manager->flush();
            $this->addFlash('success',"l'annonce <i class=\"fas fa-angle-double-left\"></i><em> {$ad->getTitle()} </em><i class=\"fas fa-angle-double-right\"></i> a bien été supprimée.");
        }
            
        return $this->redirectToRoute("admin_ads_list");
    }
}
