<?php

namespace App\Service\Crawler;


use App\Service\CrawlerResponse;
use App\Service\PriceGuess;
use Symfony\Component\DomCrawler\Crawler;

class Kika extends AbstractCrawler
{

    public function extractPrice(CrawlerResponse $crawlerResponse): ?PriceGuess
    {
        $crawler = new Crawler($crawlerResponse->getResponseBody());
        $crawler = $crawler->filter('div[data-gae-product-price]');

        if($crawler->count() === 0) {
            throw new \Exception('couldnt find div with price data attribute');
        }

        $price = $crawler->attr('data-gae-product-price');
        $price = floatval($price);

	    return new PriceGuess($price, $this->getConfidenceLevelByUrl($crawlerResponse->getRequestUrl()));
    }

	protected function getApplicableUrlRegex(): ?string
	{
		return '/^https?:\/\/(www.)?kika\.at\//';
	}

}