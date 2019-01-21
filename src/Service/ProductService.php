<?php

namespace App\Service;


use App\Entity\PriceHistory;
use App\Entity\Product;
use App\Entity\RequestLog;
use App\Repository\ProductRepository;
use App\Service\Crawler\Hellweg;
use App\Service\Crawler\Hornbach;
use App\Service\Crawler\Ikea;
use App\Service\Crawler\Kika;
use App\Service\Crawler\Moebelix;
use App\Service\Crawler\Obi;
use App\Service\Crawler\PriceCrawler;
use App\Service\Crawler\XXXLutz;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

class ProductService
{

    /** @var EntityManager */
    private $entityManager;

    /** @var ProductRepository */
    private $productRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var PriceCrawler[] */
    private $crawlers = [];

    const MINIMUM_TIME_SINCE_LAST_UPDATE = '-1hour';

    public function __construct(EntityManagerInterface $entityManager,
                                ProductRepository $productRepository,
                                LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $productRepository;
        $this->logger = $logger;

        $this->crawlers = [
            new XXXLutz(),
            new Moebelix(),
            new Kika(),
            new Ikea(),
            new Obi(),
            new Hornbach(),
            new Hellweg(),
        ];
    }

    public function createProduct(string $url): Product
    {
        $product = new Product();
        $product->setName($url);
        $product->setUrl($url);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        return $product;
    }

    public function fetchProductsNeedingUpdate()
    {
        $count = $this->productRepository->countProductsNeedingUpdate((new \DateTime())->modify(self::MINIMUM_TIME_SINCE_LAST_UPDATE));
        echo $count;
    }

    public function updateAllProducts()
    {
        $products = $this->productRepository->findProductsNeedingUpdate((new \DateTime())->modify(self::MINIMUM_TIME_SINCE_LAST_UPDATE));
        foreach ($products as $product) {
            $this->updateProduct($product);
        }
        return $products;
    }

    public function updateProduct(Product $product)
    {
        $this->logger->info(sprintf('Updating product id %d', $product->getId()));
        $startTime = microtime(true);

        $html = file_get_contents($product->getUrl());

        $requestLog = new RequestLog();
        $requestLog->setUrl($product->getUrl());
        $requestLog->setDuration(microtime(true) - $startTime);
        $requestLog->setResponseBody($html);
        $requestLog->setTimestamp(new \DateTime());

        $price = $this->extractPrice($html);

        $priceHistory = new PriceHistory();
        $priceHistory->setValue($price);
        $priceHistory->setTimestamp(new \DateTime());
        $priceHistory->setRequest($requestLog);

        $product->setCurrentPrice($price);
        $product->setLastPriceUpdate(new \DateTime());

        $this->entityManager->persist($requestLog);
        $this->entityManager->persist($priceHistory);
        $this->entityManager->flush();
    }

    /**
     * @param $html
     * @return float
     * @throws \Exception
     */
    public function extractPrice($html): float
    {
        $price = 0.0;
        foreach ($this->crawlers as $crawler) {
            try {
                $price = $crawler->extractPrice($html);
            } catch (\Exception $exception) {
                //do nuthin?
            }

            if($price > 0) {
                break;
            }
        }

        if ($price <= 0) {
            throw new \Exception('invalid zero or negative price');
        }
        return $price;
    }

}