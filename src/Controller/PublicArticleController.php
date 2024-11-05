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
    public function show(Article $article, EntityManagerInterface $em, int $id): Response
    {
        $sections = $em->getRepository(Section::class)->findAll();
        $sectionCount = $em->getRepository(Section::class)->getArticleCountPerSection();
        $authors = $this->articleRepository->getAuthors($em);

        $comments = $this->articleRepository->createQueryBuilder('a')
            ->leftJoin('a.comments', 'c')
            ->leftJoin('App\Entity\User', 'commentUser', 'WITH', 'commentUser.id = c.user_id')
            ->addSelect('c', 'commentUser')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();


/*
 * This is the results of dd($artComms)
array:2 [▼
  0 => App\Entity\Article {#846 ▼
    -id: 1
    -user_id: 8
    -title: "This time there were TWO little."
    -title_slug: "this-time-there-were-two-little"
    -text: """
      Cat, as soon as she spoke. Alice did not like to drop the jar for fear of their hearing her; and when she had found the fan.
      Alice joined the procession, wondering very much to-night, I should frighten them out again. That's all.' 'Thank you,'.
      Majesty,' said the Caterpillar. Alice said with a yelp of delight, and rushed at the bottom of the cupboards as she could have told you that.' 'If I'd been the.
 ▶
      Alice hastily replied; 'only one doesn't like changing so often, you know.' 'Who is it twelve? I--' 'Oh, don't talk about wasting IT. It's HIM.' 'I don't know m
 ▶

      Dormouse,' thought Alice; 'but a grin without a cat! It's the most curious thing I ask! It's always six o'clock now.' A bright idea came into Alice's head. 'Is
 ▶
      Alice could think of anything else. CHAPTER V. Advice from a bottle marked 'poison,' so Alice went on, yawning.

      Grief, they used to do:-- 'How doth the little--"' and she was ready to sink into the wood to.

      King. On this the White Rabbit as he fumbled over the list, feeling very curious thing, and she was going to say,' said the Hatter. 'You might just as she came.
 ▶

      Dormouse followed him: the March Hare interrupted in a mournful tone, 'he won't do a thing I ever was at the frontispiece if you cut your finger VERY deeply wit
 ▶

      MUST be more to come, so she set to partners--' '--change lobsters, and retire in same order,' continued the Pigeon, raising its voice to a snail.
      """
    -article_date_created: DateTime @1707407939 {#840 ▶}
    -article_date_posted: DateTime @1710790217 {#819 ▶}
    -published: true
    -sections: Doctrine\ORM\PersistentCollection {#865 ▶}
    -comments: Doctrine\ORM\PersistentCollection {#888 ▶}
    -user: Proxies\__CG__\App\Entity\User {#921 ▶}
  }
  1 => App\Entity\User {#1176 ▼
    -id: 32
    -username: "user24"
    -roles: array:1 [▶]
    -password: "$2y$13$ay8IBNXXHG2aoB7hMfybXOnR249PV/VX.d6/dnOei1ZhlIaVjj8A6"
    -fullname: "Sebastian Green"
    -uniqid: "user_6728d6904e7160.56960191"
    -email: "bmatthews@yahoo.co.uk"
    -activate: false
    -img_loc: "https://randomuser.me/api/portraits/women/48.jpg"
    -quote: "In another moment that it was the first question, you know.' 'Not the same thing as "I eat what I."
    -articles: Doctrine\ORM\PersistentCollection {#1175 ▶}
  }
]
 */

        return $this->render('public_article/show.html.twig', [
            'article' => $article,
            'sections' => $sections,
            'sectionCount' => $sectionCount,
            'authors' => $authors,
            'comments' => $comments,
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
