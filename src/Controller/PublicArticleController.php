<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Section;
use App\Entity\User;
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

    #[Route('/{id}/{slug}', name: 'public_article_show', requirements: ['id' => '\d+', 'slug' => '.+'], methods: ['GET'])]
    public function show(EntityManagerInterface $em, int $id): Response
    {
        $sections = $em->getRepository(Section::class)->findAll();
        $sectionCount = $em->getRepository(Section::class)->getArticleCountPerSection();
        $authors = $this->articleRepository->getAuthors($em);

        $article = $this->articleRepository->getCommentsByArticle($em, $id);


        return $this->render('public_article/show.html.twig', [
            'article' => $article,
            'sections' => $sections,
            'sectionCount' => $sectionCount,
            'authors' => $authors,

        ]);
    }

    #[Route('/author/{id}', name: 'public_article_author', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function author(EntityManagerInterface $em, int $id): Response
    {


        $author = $em->getRepository(User::class)->find($id);
        $authorId = $author->getId();

        $articles = $this->articleRepository->getArticlesByAuthor($em, $authorId);
        $artCount = count($articles);

        return $this->render('public_article/author.html.twig', [
            'author' => $author,
            'articles' => $articles,
            'artCount' => $artCount,
        ]);

    }

    #[Route('/section/{slug}', name: 'public_article_section', requirements: ['slug' => '.+'], methods: ['GET'])]
    public function section(EntityManagerInterface $em, string $slug): Response
    {

        $articles = $this->articleRepository->getArticlesBySection($em, $slug);
        $section = $em->getRepository(Section::class)->findOneBy(['section_slug' => $slug])->getSectionTitle();
        $sections = $em->getRepository(Section::class)->findAll();
        $sectionCount = $em->getRepository(Section::class)->getArticleCountPerSection();
        $authors = $em->getRepository(Article::class)->getAuthors($em);

        return $this->render('public_article/section.html.twig', [
            'articles' => $articles,
            'section' => $section,
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
