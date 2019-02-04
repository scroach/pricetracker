<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

	/**
	 * @param \DateTime $date
	 * @param int $maxResults
	 * @return Product[]
	 */
    public function findProductsNeedingUpdate(\DateTime $date, int $maxResults = 100)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.lastPriceUpdate IS NULL')
            ->orWhere('p.lastPriceUpdate < :date')->setParameter('date', $date)
            ->orderBy('p.lastPriceUpdate', 'ASC')
            ->setMaxResults($maxResults)
            ->getQuery()
            ->getResult()
        ;
    }

    public function countProductsNeedingUpdate(\DateTime $date): int
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.lastPriceUpdate IS NULL')
            ->orWhere('p.lastPriceUpdate < :date')->setParameter('date', $date)
            ->orderBy('p.lastPriceUpdate', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

}
