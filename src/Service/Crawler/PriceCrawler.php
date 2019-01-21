<?php

namespace App\Service\Crawler;

interface PriceCrawler
{
    public function extractPrice(string $html): ?float ;
}