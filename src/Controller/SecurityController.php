<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère les erreurs de connexion s'il y en a
        $error = $authenticationUtils->getLastAuthenticationError();

        // Dernier nom d'utilisateur entré par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Si l'utilisateur est déjà connecté mais non vérifié
    if ($this->getUser()) {
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('info', 'Vous devez vérifier votre email, mais vous pouvez toujours modifier votre profil.');
            return $this->redirectToRoute('app_account');  // Redirige vers la page du compte
        }

        // Si l'utilisateur est connecté et vérifié, redirigez-le vers la page d'accueil ou un autre endroit
        $this->addFlash('info', 'Vous êtes déjà connecté(e)!');
        return $this->redirectToRoute('app_home');
    }

    return $this->render('security/login.html.twig', [
        'last_username' => $lastUsername,
        'error' => $error,
    ]);
}

    #[Route(path: '/post_login', name: 'app_post_login')]
    public function postLogin(): Response
    {
        // Si l'utilisateur vient de se connecter avec succès, ajoutez un message de bienvenue
        if ($this->getUser()) {
            $this->addFlash('success', '👋 Bienvenue cher ' . $this->getUser()->getFirstName() . '!');
            return $this->redirectToRoute('app_account');  // Redirige vers la page du compte
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

