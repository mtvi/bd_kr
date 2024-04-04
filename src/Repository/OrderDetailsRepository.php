<?php

namespace App\Repository;

use App\Entity\OrderDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderDetails>
 *
 * @method OrderDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderDetails[]    findAll()
 * @method OrderDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderDetails::class);
    }

    public function search($value): array
    {
        $value = strtolower($value);

        $qb = $this->createQueryBuilder('d');
        $qb->join('d.Order', 'o');
        $qb->join('d.Product', 'p');
        $qb->join('p.Vendor', 'v');
        $qb->join('p.GPU', 'g');

        // Combine GPU and Vendor names into one field
        $nameConcat = $qb->expr()->concat('v.Name', $qb->expr()->literal(' '), 'g.Name');

        $qb->where(
            $qb->expr()->orX(
                $qb->expr()->like('LOWER(' . $nameConcat . ')', ':value'),
                $qb->expr()->like('LOWER(o.CustomerName)', ':value'),
                $qb->expr()->like('LOWER(o.Address)', ':value'),
                $qb->expr()->like('LOWER(o.Email)', ':value')
            )
        )
            ->setParameter('value', '%' . $value . '%');




        $result = $qb->getQuery()->getResult();

        return $result;
    }
    //    /**
    //     * @return OrderDetails[] Returns an array of OrderDetails objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?OrderDetails
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
