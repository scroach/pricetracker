<?php

namespace App\Service\Crawler;


use App\Service\CrawlerResponse;
use App\Service\PriceGuess;
use Symfony\Component\DomCrawler\Crawler;

class Aliexpress extends AbstractCrawler
{

	public function extractPrice(CrawlerResponse $crawlerResponse): ?PriceGuess
	{
		$crawler = new Crawler($crawlerResponse->getResponseBody());

		$priceTag = $crawler->filter('[itemprop="price"]');
		if ($priceTag->count() === 0) {
			throw new \Exception('couldnt find meta tag with price');
		}
		$price = floatval($priceTag->text());

		$currencyTag = $crawler->filter('[itemprop="priceCurrency"]');
		$currency = $currencyTag->count() ? $currencyTag->attr('content') : '€';

		return new PriceGuess($price, $this->getConfidenceLevelByUrl($crawlerResponse->getRequestUrl()), $currency);
	}

	protected function getApplicableUrlRegex(): ?string
	{
		return '/^https?:\/\/(\w?+\.)?aliexpress\.com\//';
	}

}