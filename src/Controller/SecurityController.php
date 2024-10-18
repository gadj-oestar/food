<?php

// namespace App\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\RedirectResponse;
// use Symfony\Component\Routing\Annotation\Route;

// class SecurityController extends AbstractController
// {
//     #[Route(path: '/login', name: 'app_login')]
//     public function login(): RedirectResponse
//     {
//         // Redirige vers le LoginController
//         return $this->redirectToRoute('app_login');
//     }

//     #[Route(path: '/logout', name: 'app_logout')]
//     public function logout(): void
//     {
//         throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
//     }
// }