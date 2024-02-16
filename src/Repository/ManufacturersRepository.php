<?php

namespace App\Repository;

use App\Entity\Manufacturers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Manufacturers>
 *
 * @method Manufacturers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Manufacturers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Manufacturers[]    findAll()
 * @method Manufacturers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ManufacturersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Manufacturers::class);
    }
    public function search($value, $sortBy, $sortDir): array
    {
        $value = strtolower($value);

        $qb = $this->createQueryBuilder('e');


        $qb->where($qb->expr()->orX(
            $qb->expr()->like('LOWER(e.ManufacturerName)', ':value'),
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
//     * @return Manufacturers[] Returns an array of Manufacturers objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Manufacturers
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
