<?php

namespace App\Service\Crawler;


use App\Service\CrawlerResponse;
use App\Service\PriceGuess;

class Hornbach extends AbstractCrawler
{

    public function extractPrice(CrawlerResponse $crawlerResponse): ?PriceGuess
    {
        preg_match('/"price":"([\d,.]+)"/', $crawlerResponse->getResponseBody(), $matches);

        if(!isset($matches[1])) {
            throw new \Exception('couldnt find div with price data attribute');
        }

        $price = $matches[1];
        $price = floatval($price);

	    return new PriceGuess($price, $this->getConfidenceLevelByUrl($crawlerResponse->getRequestUrl()));
    }

	protected function getApplicableUrlRegex(): ?string
	{
		return '/^https?:\/\/(www.)?hornbach\.at\//';
	}

}