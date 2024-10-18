<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

class ProductController extends AbstractController
{
    #[Route('/admin/products', name: 'admin_products')]
    public function listProducts(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les produits de la base de données
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('admin/products.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/admin/products/new', name: 'admin_new_product')]
    public function newProduct(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('product_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal pendant l'upload
                }

                $product->setImage($newFilename);
            }

            // Sauvegarder le produit dans la base de données
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Produit ajouté avec succès !');

            // Rediriger vers la liste des produits après l'ajout
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/new_product.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/products/edit/{id}', name: 'admin_edit_product')]
    public function editProduct(Request $request, Product $product, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Convertir le nom du fichier image en un objet File si l'image existe
        if ($product->getImage()) {
            $product->setImage(
                new File($this->getParameter('product_images_directory') . '/' . $product->getImage())
            );
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('product_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe mal pendant l'upload
                }

                $product->setImage($newFilename);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Produit mis à jour avec succès !');

            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin/edit_product.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/admin/products/delete/{id}', name: 'admin_delete_product')]
    public function deleteProduct(Product $product, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($product);
        $entityManager->flush();

        $this->addFlash('success', 'Produit supprimé avec succès !');

        return $this->redirectToRoute('admin_products');
    }
}
