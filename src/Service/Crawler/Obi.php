<?php

namespace App\Service\Crawler;


use App\Service\CrawlerResponse;
use App\Service\PriceGuess;
use Symfony\Component\DomCrawler\Crawler;

class Obi extends AbstractCrawler
{

    public function extractPrice(CrawlerResponse $crawlerResponse): ?PriceGuess
    {
        $crawler = new Crawler($crawlerResponse->getResponseBody());
        $crawler = $crawler->filter('strong[itemprop="price"]');

        if($crawler->count() === 0) {
            throw new \Exception('couldnt find strong tag with price');
        }

        $price = $crawler->text();
        $price = floatval(str_replace(',', '.', str_replace('.', '', $price)));

	    return new PriceGuess($price, $this->getConfidenceLevelByUrl($crawlerResponse->getRequestUrl()));
    }

	protected function getApplicableUrlRegex(): ?string
	{
		return '/^https?:\/\/(www.)?obi\.at\//';
	}

}