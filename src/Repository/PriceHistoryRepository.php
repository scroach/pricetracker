<?php

namespace App\Repository;

use App\Entity\PriceHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PriceHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method PriceHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method PriceHistory[]    findAll()
 * @method PriceHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PriceHistoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PriceHistory::class);
    }

}
