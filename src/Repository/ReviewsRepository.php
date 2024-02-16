<?php

namespace App\Repository;

use App\Entity\Reviews;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reviews>
 *
 * @method Reviews|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reviews|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reviews[]    findAll()
 * @method Reviews[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reviews::class);
    }
    public function search($value, $productId, $sortBy, $sortDir): array
    {
        $value = strtolower($value);

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.Product', 'p');
        $qb->join('p.Vendor', 'v');
        $qb->join('p.GPU', 'g');


        $nameConcat = $qb->expr()->concat('v.Name', $qb->expr()->literal(' '), 'g.Name');
        
        $qb->where($qb->expr()->orX(
            $qb->expr()->like('LOWER(' . $nameConcat . ')', ':value'),
            $qb->expr()->like('LOWER(e.ReviewText)', ':value'),
            $qb->expr()->like('LOWER(e.Reviewer)', ':value')
        )
        )
            ->setParameter('value', '%' . $value . '%');

        // Add conditions for GPU and Vendor if they are provided
        if ($productId) {
            $qb->andWhere('p.id = :productId')
                ->setParameter('productId', $productId);
        }

        $qb->orderBy('e.'. $sortBy, ($sortDir === 'asc' ? 'ASC' : 'DESC'));
        
        $result = $qb->getQuery()->getResult();

        return $result;
    }
    public function findByProductId($productId, $sortDir)
    {
        return $this->createQueryBuilder('e')
            ->join('e.Product', 'p')
            ->andWhere('p.id = :productId')
            ->setParameter('productId', $productId)
            ->orderBy('e.ReviewDate',$sortDir)
            ->getQuery()
            ->getResult();
    }
//    /**
//     * @return Reviews[] Returns an array of Reviews objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reviews
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
