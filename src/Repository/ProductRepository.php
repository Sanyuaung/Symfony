<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findAllWithFilters(array $searchParams = [])
    {
        $qb = $this->createQueryBuilder('p');

        // Filter by name
        if (!empty($searchParams['name'])) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $searchParams['name'] . '%');
        }

        // Filter by price range
        if (!empty($searchParams['priceRange'][0]) && !empty($searchParams['priceRange'][1])) {
            $qb->andWhere('p.price BETWEEN :minPrice AND :maxPrice')
                ->setParameter('minPrice', $searchParams['priceRange'][0])
                ->setParameter('maxPrice', $searchParams['priceRange'][1]);
        }

        // Filter by stock quantity
        if (!empty($searchParams['stockQuantity'])) {
            $qb->andWhere('p.stockQuantity >= :stockQuantity')
                ->setParameter('stockQuantity', $searchParams['stockQuantity']);
        }

        // Filter by created datetime
        if (!empty($searchParams['createdDatetime'])) {
            $qb->andWhere('p.createdDatetime >= :createdDatetime')
                ->setParameter('createdDatetime', $searchParams['createdDatetime']);
        }

        return $qb->getQuery()->getResult();
    }
}
