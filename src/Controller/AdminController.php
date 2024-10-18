<?php namespace App\Controller;


use App\Entity\Email;  // Remplace par ton entité utilisateur
use App\Form\UserEditFormType;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_users')]
    public function listUsers(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(Email::class)->findAll();  // Remplace par ta classe utilisateur

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/users/edit/{id}', name: 'admin_edit_user')]
public function editUser(Request $request, Email $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(UserEditFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        if ($form->get('plainPassword')->getData()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setPassword($hashedPassword);
        }

        // Mise à jour des rôles
        $user->setRoles($form->get('roles')->getData());

        $entityManager->flush();
        return $this->redirectToRoute('admin_users');
    }

    return $this->render('admin/edit_user.html.twig', [
        'userForm' => $form->createView(),
        'user' => $user,
    ]);
    }

    #[Route('/admin/users/delete/{id}', name: 'admin_delete_user')]
    public function deleteUser(Email $user, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('admin_users');
    }
    #[Route('/admin/products', name: 'admin_products')]
    public function listProducts(EntityManagerInterface $entityManager): Response
    {
        // Récupérer tous les produits depuis la base de données
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('admin/products.html.twig', [
            'products' => $products,
        ]);
    }
}
