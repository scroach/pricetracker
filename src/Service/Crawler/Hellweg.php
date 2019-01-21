<?php

namespace App\Service\Crawler;


use Symfony\Component\DomCrawler\Crawler;

class Hellweg implements PriceCrawler
{

    public function extractPrice(string $html): ?float
    {
        $crawler = new Crawler($html);
        $crawler = $crawler->filter('.product-actions');

        if($crawler->count() === 0) {
            throw new \Exception('couldnt find div with price data attribute');
        }

        $price = $crawler->attr('data-price');
        $price = floatval($price);

        return $price;
    }

}