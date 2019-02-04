<?php

namespace App\Service\Crawler;


use App\Service\CrawlerResponse;
use App\Service\PriceGuess;
use Symfony\Component\DomCrawler\Crawler;

class Ikea extends AbstractCrawler
{

    public function extractPrice(CrawlerResponse $crawlerResponse): ?PriceGuess
    {
        $crawler = new Crawler($crawlerResponse->getResponseBody());
        $crawler = $crawler->filter('meta[itemprop="price"]');

        if($crawler->count() === 0) {
            throw new \Exception('couldnt find meta tag with price');
        }

        $price = $crawler->attr('content');
        $price = floatval($price);

	    return new PriceGuess($price, $this->getConfidenceLevelByUrl($crawlerResponse->getRequestUrl()));
    }

	protected function getApplicableUrlRegex(): ?string
	{
		return '/^https?:\/\/(www.)?ikea\.com\//';
	}

}