<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Section;
use App\Entity\User;
use App\Form\User1Type;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/public/user')]
final class PublicUserController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/new', name: 'public_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $textPass = $form->get('password')->getData();

            $hashPass = $this->passwordHasher->hashPassword($user, $textPass);
            $user->setPassword($hashPass);

            $user->setRoles(["ROLE_USER"]);
            $user->setUniqid(uniqid('user_', true));
            $user->setActivate(false);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('public_article_index', [], Response::HTTP_SEE_OTHER);
        }
        $sections = $entityManager->getRepository(Section::class)->findAll();
        $authors = $entityManager->getRepository(Article::class)->getAuthors($entityManager);
        return $this->render('public_user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'sections' => $sections,
            'authors' => $authors,
        ]);
    }

    /*
    #[Route(name: 'app_public_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('public_user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_public_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('public_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_public_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_public_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('public_user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_public_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_public_user_index', [], Response::HTTP_SEE_OTHER);
    }

    */

}
