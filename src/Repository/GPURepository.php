<?php

namespace App\Repository;

use App\Entity\GPU;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GPU>
 *
 * @method GPU|null find($id, $lockMode = null, $lockVersion = null)
 * @method GPU|null findOneBy(array $criteria, array $orderBy = null)
 * @method GPU[]    findAll()
 * @method GPU[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GPURepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GPU::class);
    }
    public function search($value, $manufacturerId, $memoryId, $pciId, $categoryId, $sortBy, $sortDir): array
    {
        $value = strtolower($value);

        $qb = $this->createQueryBuilder('g');
        $qb->join('g.Manufacturer', 'm');
        $qb->join('g.Memory', 't');
        $qb->join('g.PCIVersion', 'p');
        $qb->join('g.Category', 'c');


        $qb->where(
            $qb->expr()->like('LOWER(g.Name)', ':value')
        )
            ->setParameter('value', '%' . $value . '%');

        // Add conditions for GPU and Vendor if they are provided
        if ($manufacturerId) {
            $qb->andWhere('m.id = :manufacturerId')
                ->setParameter('manufacturerId', $manufacturerId);
        }

        if ($memoryId) {
            $qb->andWhere('t.id = :memoryId')
                ->setParameter('memoryId', $memoryId);
        }
        if ($pciId) {
            $qb->andWhere('p.id = :pciId')
                ->setParameter('pciId', $pciId);
        }
        if ($categoryId) {
            $qb->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        $qb->orderBy('g.' . $sortBy, ($sortDir === 'asc' ? 'ASC' : 'DESC'));

        $result = $qb->getQuery()->getResult();

        return $result;
    }
    //    /**
//     * @return GPU[] Returns an array of GPU objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    //    public function findOneBySomeField($value): ?GPU
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
