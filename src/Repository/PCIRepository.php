<?php

namespace App\Repository;

use App\Entity\PCI;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PCI>
 *
 * @method PCI|null find($id, $lockMode = null, $lockVersion = null)
 * @method PCI|null findOneBy(array $criteria, array $orderBy = null)
 * @method PCI[]    findAll()
 * @method PCI[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PCIRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PCI::class);
    }
    public function search($value, $sortBy, $sortDir): array
    {
        $value = strtolower($value);

        $qb = $this->createQueryBuilder('e');


        $qb->where($qb->expr()->like('LOWER(e.Version)', ':value'))
            ->setParameter('value', '%' . $value . '%');

        $qb->orderBy('e.'. $sortBy, ($sortDir === 'asc' ? 'ASC' : 'DESC'));
        
        $result = $qb->getQuery()->getResult();

        return $result;
    }
//    /**
//     * @return PCI[] Returns an array of PCI objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PCI
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
