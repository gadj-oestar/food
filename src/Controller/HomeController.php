<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        // Récupérer les produits depuis le repository
        $products = $productRepository->findAll();

        // Passer la variable products au template Twig
        return $this->render('home/index.html.twig', [
            'products' => $products,
        ]);
    }
}
