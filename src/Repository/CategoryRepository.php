<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * @return Category[]
     */
    public function findAllOrdered(): array
    {
        // $dql = "SELECT category FROM App\Entity\Category category ORDER BY category.name ASC";

        $qb = $this->createQueryBuilder('category');
        $qb->orderBy('category.name', Criteria::ASC);
        $query = $qb->getQuery();

        // $query = $this->getEntityManager()->createQuery($dql);
        // dd($query->getSQL());
        return $query->getResult();
    }

    /**
     * @return Category[]
     */
    public function search(string $term): array
    {
        $qb = $this->addOrderByCategoryName();

        $qb = $this->addFortuneCookieJoinAndSelect($qb)
        ->andWhere('category.name LIKE :term OR category.iconKey LIKE :term OR fortuneCookie.fortune LIKE :term')
        // ->orWhere('category.iconKey LIKE :term')
        ->setParameter('term', '%' . $term . '%');

        $query = $qb->getQuery();
        return $query->getResult();
    }

    function findWithFortuneCookies(int $id): ?Category
    {
        return $this->addFortuneCookieJoinAndSelect()
            ->andWhere('category.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    function addFortuneCookieJoinAndSelect(?QueryBuilder $qb = null): QueryBuilder
    {
        return ($qb ?? $this->createQueryBuilder('category'))
            ->addSelect('fortuneCookie')
            ->leftJoin('category.fortuneCookies', 'fortuneCookie');
    }

    function addOrderByCategoryName(?QueryBuilder $qb = null): QueryBuilder
    {
        return ($qb ?? $this->createQueryBuilder('category'))
            ->orderBy('category.name', Criteria::ASC);
    }

    public function save(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Category $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Category[] Returns an array of Category objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
