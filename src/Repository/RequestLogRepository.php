<?php

namespace App\Repository;

use App\Entity\RequestLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RequestLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method RequestLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method RequestLog[]    findAll()
 * @method RequestLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RequestLogRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RequestLog::class);
    }

}
