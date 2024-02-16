<?php

namespace App\Repository;

use App\Entity\Vendors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vendors>
 *
 * @method Vendors|null find($id, $lockMode = null, $lockVersion = null)
 * @method Vendors|null findOneBy(array $criteria, array $orderBy = null)
 * @method Vendors[]    findAll()
 * @method Vendors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VendorsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vendors::class);
    }
    public function search($value, $sortBy, $sortDir): array
    {
        $value = strtolower($value);

        $qb = $this->createQueryBuilder('e');


        $qb->where($qb->expr()->orX(
            $qb->expr()->like('LOWER(e.Name)', ':value'),
            $qb->expr()->like('LOWER(e.Website)', ':value'),
            $qb->expr()->like('LOWER(e.Country)', ':value')
        )
        )
            ->setParameter('value', '%' . $value . '%');

        $qb->orderBy('e.'. $sortBy, ($sortDir === 'asc' ? 'ASC' : 'DESC'));
        
        $result = $qb->getQuery()->getResult();

        return $result;
    }

//    /**
//     * @return Vendors[] Returns an array of Vendors objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('v.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Vendors
//    {
//        return $this->createQueryBuilder('v')
//            ->andWhere('v.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
