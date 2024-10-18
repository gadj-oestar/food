<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart_index')]
    public function index(SessionInterface $session, ProductRepository $productRepository): Response
    {
        // Récupérer les produits du panier depuis la session
        $cart = $session->get('cart', []);

        $cartWithData = [];
        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        // Calcul du total
        $total = 0;
        foreach ($cartWithData as $item) {
            $total += $item['product']->getPrice() * $item['quantity'];
        }

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add($id, SessionInterface $session)
    {
        // Récupérer le panier actuel
        $cart = $session->get('cart', []);

        // Ajouter le produit au panier
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        // Mettre à jour le panier dans la session
        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit ajouté au panier.');

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, SessionInterface $session)
    {
        // Récupérer le panier actuel
        $cart = $session->get('cart', []);

        // Supprimer le produit du panier
        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        // Mettre à jour la session
        $session->set('cart', $cart);

        $this->addFlash('success', 'Produit supprimé du panier.');

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/update/{id}', name: 'cart_update')]
    public function update($id, Request $request, SessionInterface $session)
    {
        // Récupérer la nouvelle quantité depuis la requête
        $quantity = $request->request->get('quantity');

        // Récupérer le panier actuel
        $cart = $session->get('cart', []);

        // Mettre à jour la quantité du produit dans le panier
        if (!empty($cart[$id])) {
            $cart[$id] = $quantity;
        }

        // Mettre à jour le panier dans la session
        $session->set('cart', $cart);

        $this->addFlash('success', 'Quantité mise à jour.');

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/cart/clear', name: 'cart_clear')]
    public function clear(SessionInterface $session)
    {
        // Vider le panier
        $session->remove('cart');

        $this->addFlash('success', 'Panier vidé.');

        return $this->redirectToRoute('cart_index');
    }
}
