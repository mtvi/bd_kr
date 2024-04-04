<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Products>
 *
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }


    public function search($value, $gpuIds, $vendorIds,$manufcaturerIds, $memoryIds, $pciIds, $categoryIds, $mCountries, $vCountries, $startPrice, $endPrice, $sortBy, $sortDir): array
    {
        $value = strtolower($value);

        $qb = $this->createQueryBuilder('e');
        $qb->join('e.Vendor', 'v');
        $qb->join('e.GPU', 'g');
        $qb->join('g.Manufacturer', 'man');
        $qb->join('g.Memory', 'mt');
        $qb->join('g.PCIVersion', 'pci');
        $qb->join('g.Category', 'c');
        // Combine GPU and Vendor names into one field
        $nameConcat = $qb->expr()->concat('v.Name', $qb->expr()->literal(' '), 'g.Name');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->like('LOWER(' . $nameConcat . ')', ':value'),
                $qb->expr()->like('LOWER(e.CoolingType)', ':value')
            )
        )
            ->setParameter('value', '%' . $value . '%');

        // Add conditions for GPU and Vendor if they are provided
        if (!empty($gpuIds)) {
            $qb->andWhere('g.id IN (:gpuIds)')
                ->setParameter('gpuIds', $gpuIds);
        }

        if (!empty($vendorIds)) {
            $qb->andWhere('v.id IN (:vendorIds)')
                ->setParameter('vendorIds', $vendorIds);
        }

        if (!empty($manufcaturerIds)) {
            $qb->andWhere('man.id IN (:manufcaturerIds)')
                ->setParameter('manufcaturerIds', $manufcaturerIds);
        }
        if (!empty($memoryIds)) {
            $qb->andWhere('mt.id IN (:memoryIds)')
                ->setParameter('memoryIds', $memoryIds);
        }
        if (!empty($pciIds)) {
            $qb->andWhere('pci.id IN (:pciIds)')
                ->setParameter('pciIds', $pciIds);
        }
        if (!empty($categoryIds)) {
            $qb->andWhere('c.id IN (:categoryIds)')
                ->setParameter('categoryIds', $categoryIds);
        }
        if (!empty($mCountries)) {
            $qb->andWhere('man.Country IN (:mCountries)')
                ->setParameter('mCountries', $mCountries);
        }
        if (!empty($vCountries)) {
            $qb->andWhere('v.Country IN (:vCountries)')
                ->setParameter('vCountries', $vCountries);
        }
        if (!empty($startPrice)) {
            $qb->andWhere('e.Price >+ (:startPrice)')
                ->setParameter('startPrice', $startPrice);
        }
        if (!empty($endPrice)) {
            $qb->andWhere('e.Price <= (:endPrice)')
                ->setParameter('endPrice', $endPrice);
        }

        if ($sortBy == 'ReleaseDate') {
            $qb->orderBy('g.' . $sortBy, $sortDir);
        } else {
            $qb->orderBy('e.' . $sortBy, $sortDir);
        }
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    


    //    /**
    //     * @return Products[] Returns an array of Products objects
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

    //    public function findOneBySomeField($value): ?Products
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
