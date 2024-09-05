<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\File;
use DateTimeImmutable;
use App\Form\UserFormType;
use App\Repository\VideoRepository;



class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function show(VideoRepository $videoRepository): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        
        if (!$user) {
            // Ajouter un message flash pour informer l'utilisateur
            $this->addFlash('error', "Vous n'avez pas de compte, créez en un!");
            return $this->redirectToRoute('app_register'); 
        }

        // Si l'utilisateur est connecté et vérifié, afficher la page du profil
        if (!$user->isVerified()) {
            // L'utilisateur peut accéder à son compte mais on lui rappelle de vérifier son e-mail
            $this->addFlash('warning bg-blue-600', "✉️ Votre adresse e-mail n'est pas vérifié. Veuillez confirmer pour un avoir un accès complet au site.");
        }
    
        // Récupérer les vidéos de l'utilisateur connecté
        $videos = $videoRepository->findBy(['user' => $user]);
    
        return $this->render('account/show.html.twig', [
            'videos' => $videos,
            'user' => $user,
        ]);
        
    }


    #[Route('/account/edit', name: "app_account_edit")]
    public function edit(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): Response{
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload de l'avatar
            $avatarFile = $form->get('avatarFile')->getData();

            if ($avatarFile) {
                $user->setAvatarFile($avatarFile); // Le VichUploaderBundle s'occupe de l'upload
            }

            $user->setUpdatedAt(new DateTimeImmutable());
            $em->flush();

            $this->addFlash('success', $translator->trans('Votre compte a bien été mis à jour !'));
            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/edit.html.twig', [
            'user' => $user,
            'monForm' => $form->createView(),
        ]);
    }
}
