<?php

namespace App\Service;


use App\Entity\PriceHistory;
use App\Entity\Product;
use App\Entity\RequestLog;
use App\Repository\ProductRepository;
use App\Service\Crawler\Aliexpress;
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
			new Aliexpress(),
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

	public function countProductsNeedingUpdate()
	{
		return $this->productRepository->countProductsNeedingUpdate((new \DateTime())->modify(self::MINIMUM_TIME_SINCE_LAST_UPDATE));
	}

	/**
	 * @param int $maxResults
	 * @return Product[]
	 */
	public function fetchProductsNeedingUpdate(int $maxResults = 100)
	{
		return $this->productRepository->findProductsNeedingUpdate((new \DateTime())->modify(self::MINIMUM_TIME_SINCE_LAST_UPDATE), $maxResults);
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

		$response = $this->performHttpRequest($product);

		$requestLog = new RequestLog();
		$requestLog->setUrl($product->getUrl());
		$requestLog->setDuration(microtime(true) - $startTime);
		$requestLog->setResponseBody($response->getResponseBody());
		$requestLog->setTimestamp(new \DateTime());

		$priceGuess = $this->extractPrice($response);

		$priceHistory = new PriceHistory();
		$priceHistory->setValue($priceGuess->getPrice());
		$priceHistory->setCurrency($priceGuess->getCurrency());
		$priceHistory->setTimestamp(new \DateTime());
		$priceHistory->setRequest($requestLog);

		$product->setCurrentPrice($priceGuess->getPrice());
		$product->setCurrency($priceGuess->getCurrency());
		$product->setLastPriceUpdate(new \DateTime());

		$this->entityManager->persist($requestLog);
		$this->entityManager->persist($priceHistory);
		$this->entityManager->flush();
	}

	public function extractPrice(CrawlerResponse $response): PriceGuess
	{
		/** @var PriceGuess|null $bestPriceGuess */
		$bestPriceGuess = null;
		foreach ($this->crawlers as $crawler) {
			try {
				$newGuess = $crawler->extractPrice($response);
				if ($bestPriceGuess === null || $newGuess->getConfidenceLevel() > $bestPriceGuess->getConfidenceLevel()) {
					$bestPriceGuess = $newGuess;
				}
			} catch (\Exception $exception) {
				//do nuthin?
			}
		}

		if ($bestPriceGuess === null || $bestPriceGuess->getPrice() <= 0) {
			throw new \Exception('invalid zero or negative price');
		}
		return $bestPriceGuess;
	}

	private function performHttpRequest(Product $product): CrawlerResponse
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $product->getUrl());
		curl_setopt($ch, CURLOPT_USERAGENT, UserAgent::CHROME_MOBILE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($ch);
		if ($response === false) {
			throw new \Exception('error during curl request: ' . curl_error($ch));
		}

		$response = new CrawlerResponse($product->getUrl(), $response);
		return $response;
	}

}