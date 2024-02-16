<?php

namespace App\Repository;

use App\Entity\Categories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categories>
 *
 * @method Categories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categories[]    findAll()
 * @method Categories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categories::class);
    }
    public function search($value): array
    {
        $value = strtolower($value);

        $qb = $this->createQueryBuilder('e');


        // $qb->where($qb->expr()->orX(
        //     $qb->expr()->like('LOWER(e.ManufacturerName)', ':value'),
        //     $qb->expr()->like('LOWER(e.Website)', ':value'),
        //     $qb->expr()->like('LOWER(e.Country)', ':value')
        // )
        // )
        //     ->setParameter('value', '%' . $value . '%');
        $qb->where($qb->expr()->like('LOWER(e.CategoryName)', ':value'))
            ->setParameter('value', '%' . $value . '%');

            
        $result = $qb->getQuery()->getResult();

        return $result;
    }
//    /**
//     * @return Categories[] Returns an array of Categories objects
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

//    public function findOneBySomeField($value): ?Categories
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
