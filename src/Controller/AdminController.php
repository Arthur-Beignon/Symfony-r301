<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Security\Http\Attribute\IsGranted;



#[IsGranted('ROLE_USER', statusCode: 403, message: 'You must be logged in.')] 
class AdminController extends AbstractController {
     #[Route('/admin', name: 'app_admin')] 
     public function index(): Response { 
        return $this->render('admin/index.html.twig', [ 
            'controller_name' => 'AdminController', 
        ]); 
    }

    #[Route('/admin/test1', name: 'app_admin_test1')] 
    public function index_test(): Response { 
        $this->denyAccessUnlessGranted('ROLE_ADMIN'); 
        // or add an optional message - seen by developers 
        $this->denyAccessUnlessGranted('ROLE_ADMIN', null, 'User tried to access a page without having ROLE_ADMIN');
         return $this->render('admin/index.html.twig', [
             'controller_name' => 'AdminController', 
            ]);
    }

    #[Route('/admin/test2', name: 'app_admin_test2')] 
    #[IsGranted('ROLE_SUPER_ADMIN', statusCode: 403, message: 'You are not allowed to access the Super admin dashboard.')]
     public function index_test2(): Response {
         return $this->render('admin/index.html.twig', [ 
            'controller_name' => 'AdminController', 
        ]); 
    }

}