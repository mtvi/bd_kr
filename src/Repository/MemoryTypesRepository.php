<?php

namespace App\Repository;

use App\Entity\MemoryTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MemoryTypes>
 *
 * @method MemoryTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method MemoryTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method MemoryTypes[]    findAll()
 * @method MemoryTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MemoryTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MemoryTypes::class);
    }
    public function search($value, $sortBy, $sortDir): array
    {
        $value = strtolower($value);

        $qb = $this->createQueryBuilder('e');


        $qb->where($qb->expr()->like('LOWER(e.Type)', ':value'))
            ->setParameter('value', '%' . $value . '%');

        $qb->orderBy('e.'. $sortBy, ($sortDir === 'asc' ? 'ASC' : 'DESC'));
        
        $result = $qb->getQuery()->getResult();

        return $result;
    }

//    /**
//     * @return MemoryTypes[] Returns an array of MemoryTypes objects
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

//    public function findOneBySomeField($value): ?MemoryTypes
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
