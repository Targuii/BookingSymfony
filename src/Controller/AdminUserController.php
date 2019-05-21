<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Service\Pagination;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users/{page<\d+>?1}", name="admin_users_list")
     */
    public function index(UserRepository $repo,$page,Pagination $paginationService)
    {
        $paginationService->setEntityClass(User::class)
                          ->setPage($page);

        return $this->render('admin/user/index.html.twig', [
            'pagination'=>$paginationService
        ]);
    }

    /**
     * Modification d'un profil Utilisateur
     * @Route("/admin/user/{id}/edit",name="admin_user_edit")
     *
     * @param User $user
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(User $user,Request $request,ObjectManager $manager){

        $form = $this->createForm(AdminUserType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $manager->persist($user);
            $manager->flush();

            $this->addFlash("success","L'utilisateur a été correctement modifié !");

            return  $this->redirectToRoute('admin_users_list');
        }

        return $this->render('admin/user/edit.html.twig',[
            'users'=>$user,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Suppression d'un utilisateur
     * @Route("admin/user/{id}/delete",name="admin_user_delete")
     *
     * @param User $user
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(User $user,ObjectManager $manager){

        if(count($user->getBookings()) >0){
            $this->addFlash('warning',"Interdit de supprimer un utilisateur qui possède des réservations.");

        }else{

        $manager->remove($user);
        $manager->flush();

        $this->addFlash("success","L'utilisateur {$user->getId()} a bien été supprimé !");

    }
        return $this->redirectToRoute('admin_users_list');
    }
}
