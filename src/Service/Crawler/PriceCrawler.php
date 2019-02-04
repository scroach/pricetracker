<?php

namespace App\Service\Crawler;

use App\Service\CrawlerResponse;
use App\Service\PriceGuess;

interface PriceCrawler
{
    public function extractPrice(CrawlerResponse $response): ?PriceGuess ;

	public function getConfidenceLevelByUrl(string $requestUrl);
}