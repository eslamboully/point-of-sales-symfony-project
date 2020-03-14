<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Client;
use App\Entity\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("admin", name="admin.")
 * @IsGranted("ROLE_SHOW_HOME")
 */

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $entityManager = $this->getDoctrine()->getManager();
        $categories = $entityManager->getRepository(Category::class)->findAll();
        $products = $entityManager->getRepository(Product::class)->findAll();
        $clients = $entityManager->getRepository(Client::class)->findAll();
        $admins = $entityManager->getRepository(Admin::class)->findAll();

        return $this->render('admin/index.html.twig',[
          'categories' => count($categories),
          'products' => count($products),
          'clients' => count($clients),
          'admins' => count($admins),
        ]);
    }



}
