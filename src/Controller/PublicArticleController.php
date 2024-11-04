<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Section;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
final class PublicArticleController extends AbstractController
{

    private ArticleRepository $articleRepository;
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    #[Route(name: 'public_article_index', methods: ['GET'])]
    public function index(EntityManagerInterface $em, PaginatorInterface $pagi, Request $request): Response
    {
        $sections = $em->getRepository(Section::class)->findAll();
        $sectionCount = $em->getRepository(Section::class)->getArticleCountPerSection();
        $authors = $em->getRepository(Article::class)->getAuthors($em);
        return $this->render('public_article/index.html.twig', [
            'pagination' => $this->articleRepository->getPagination($em, $pagi, $request),
            'sections' => $sections,
            'sectionCount' => $sectionCount,
            'authors' => $authors,
        ]);
    }
/*
    #[Route('/new', name: 'app_public_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_public_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('public_article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }
*/
    #[Route('/{id}', name: 'public_article_show', methods: ['GET'])]
    public function show(Article $article): Response
    {
        return $this->render('public_article/show.html.twig', [
            'article' => $article,
        ]);
    }
/*
    #[Route('/{id}/edit', name: 'app_public_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_public_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('public_article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_public_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_public_article_index', [], Response::HTTP_SEE_OTHER);
    }
*/
}
