<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/')]
class VideoController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(VideoRepository $videoRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Vérifier si l'utilisateur est connecté
        $user = $this->getUser();
        if ($user) {
            // // Vérifier si l'email de l'utilisateur est vérifié
            // if (!$user->isVerified()) {
            //     // Ajouter un message flash si l'email n'est pas vérifié
            //     $this->addFlash('info', 'Your email address is not verified.');
            // }
        }

        // Récupérer les vidéos selon que l'utilisateur est connecté et vérifié ou non
        if ($user && $user->isVerified()) {
            // Utilisateur connecté et vérifié : montrer toutes les vidéos
            $data = $videoRepository->findAll();
        } else {
            // Utilisateur non connecté ou non vérifié : montrer seulement les vidéos non premium
            $data = $videoRepository->findBy(['premiumVideo' => false]);
        }

        // Paginer les résultats
        $videos = $paginator->paginate(
            $data, // Les données à paginer
            $request->query->getInt('page', 1), // Le numéro de page, par défaut 1
            9 // Le nombre d'éléments par page
        );

        // Rendre le template avec les vidéos paginées
        return $this->render('video/index.html.twig', [
            'videos' => $videos, // Passez l'objet PaginationInterface à la vue
        ]);
    }

#[Route('/video/create', name: 'app_video_create', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    // Vérifier si l'utilisateur est connecté
    if ($this->getUser()) {
        // Vérifier si l'email de l'utilisateur est vérifié
        if (!$this->getUser()->isVerified()) {
            // Ajouter un message flash si l'email n'est pas vérifié
            // $this->addFlash('error', 'Votre adresse e-mail n\'est pas vérifiée.');
            $this->addFlash('error', '⚠️ Vous devez confirmer votre adresse email pour créer une vidéo !');
            
            return $this->redirectToRoute('app_home');  // Redirige vers la page d'accueil
        }
    } else {
        // Si l'utilisateur n'est pas connecté
        $this->addFlash('error', '⚠️ Vous devez être vérifié et connecté pour créer une vidéo.');
        return $this->redirectToRoute('app_register');  // Redirige vers la page de login
    }
    

    // Si l'utilisateur est connecté et son email est vérifié, on continue avec la création de la vidéo
    $video = new Video();
    $form = $this->createForm(VideoType::class, $video);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Associer l'utilisateur connecté à la vidéo
        $user = $this->getUser();  // Récupère l'utilisateur connecté
        $video->setUser($user);    // Associe l'utilisateur à la vidéo
        
        $video->setCreatedAt(new \DateTime());
        $video->setUpdatedAt(new \DateTime());
        $entityManager->persist($video);
        $entityManager->flush();
        $this->addFlash('success', '👍 Votre vidéo a bien été créée!');

        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('video/new.html.twig', [
        'video' => $video,
        'form' => $form,
    ]);
}

    #[Route('/video/{id}', name: 'app_video_show', methods: ['GET'])]
    public function show(Video $video): Response
    {
        if (!$this->getUser()) {
            // Ajouter un message flash pour dire qu'il faut être connecté
            $this->addFlash('error', '⚠️ Vous devez être connecté et vérifié pour voir ces vidéos.');
            return $this->redirectToRoute('app_register');
        }
        return $this->render('video/show.html.twig', [
            'video' => $video,
        ]);
    }

    #[Route('/video/{id}/edit', name: 'app_video_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Video $video, EntityManagerInterface $entityManager): Response
{
    if ($this->getUser()) {
        // Vérifiez d'abord si l'utilisateur est le propriétaire de la vidéo
        if ($video->getUser()->getEmail() !== $this->getUser()->getEmail()) {
            $this->addFlash('error', '⚠️ Vous ne pouvez modifier que vos créations !');
            return $this->redirectToRoute('app_home');
        }
        // Puis, vérifiez si l'utilisateur a confirmé son email
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('error', '⚠️ Vous devez confirmer votre email pour modifier une vidéo !');
        }
        
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', '👍 Votre vidéo a bien été modifiée !');
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('video/edit.html.twig', ['video' => $video, 'form' => $form]);
    } else {
        $this->addFlash('error', 'Vous devez être connecté pour modifier une vidéo !');
        return $this->redirectToRoute('app_login');
    }
}
#[Route('/video/{id}/delete', name: 'app_video_delete', methods: ['POST'])]
public function delete(Request $request, Video $video, EntityManagerInterface $entityManager): Response
{
    if ($this->getUser()) {
        // Vérifiez d'abord si l'utilisateur est le propriétaire de la vidéo
        if ($video->getUser()->getEmail() !== $this->getUser()->getEmail()) {
            $this->addFlash('error', '⚠️ Vous ne pouvez supprimer que vous créations !');
            return $this->redirectToRoute('app_home');
        }
        // Puis, vérifiez si l'utilisateur a confirmé son email
        if (!$this->getUser()->isVerified()) {
            $this->addFlash('error', '⚠️ Vous devez confirmer votre email pour supprimer une vidéo !');
            return $this->redirectToRoute('app_home');
        }

        // Vérification du token CSRF
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
            $entityManager->remove($video);
            $entityManager->flush();
            $this->addFlash('success', '👏 Vidéo supprimée avec succès !');
        } else {
            $this->addFlash('error', 'Échec de la suppression de la vidéo.');
        }
        return $this->redirectToRoute('app_home');
    } else {
        $this->addFlash('error', '⚠️ Vous devez être connecté pour supprimer une vidéo !');
        return $this->redirectToRoute('app_login');
    }




        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }
}
