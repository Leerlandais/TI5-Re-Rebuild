<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;


/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }
    public function getPagination(EntityManagerInterface $em, PaginatorInterface $paginator, Request $request): \Knp\Component\Pager\Pagination\PaginationInterface
    {
        $queryBuilder = $em->getRepository(Article::class)->createQueryBuilder('a')
            ->where('a.published = 1')
            ->orderBy('a.article_date_posted', 'DESC');

        return $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );
    }

    public function findAdjacentArticles(int $id, int $author): array
    {
        $mainArticle = $this->find($id);
        if (!$mainArticle) {
            return ['main' => null, 'previous' => null, 'next' => null];
        }

        $nextArticle = $this->createQueryBuilder('a')
            ->where('a.id > :id')
            ->setParameter('id', $id)
            ->andWhere('a.published = true')
            ->andWhere('a.user = :author')
            ->setParameter('author', $author)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (!$nextArticle) {
            $nextArticle = $this->createQueryBuilder('a')
                ->where('a.user = :author')
                ->setParameter('author', $author)
                ->andWhere('a.published = true')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }

        $previousArticle = $this->createQueryBuilder('a')
            ->where('a.id < :id')
            ->setParameter('id', $id)
            ->andWhere('a.user = :author')
            ->setParameter('author', $author)
            ->andWhere('a.published = true')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if (!$previousArticle) {
            $previousArticle = $this->createQueryBuilder('a')
                ->where('a.user = :author')
                ->setParameter('author', $author)
                ->andWhere('a.published = true')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }

        return [
            'main' => $mainArticle,
            'prev' => $previousArticle,
            'next' => $nextArticle,
        ];
    }
    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
