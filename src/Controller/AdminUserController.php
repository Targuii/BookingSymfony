<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Pagination;
use App\Repository\UserRepository;
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
}
