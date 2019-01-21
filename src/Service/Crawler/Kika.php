<?php

namespace App\Service\Crawler;


use Symfony\Component\DomCrawler\Crawler;

class Kika implements PriceCrawler
{

    public function extractPrice(string $html): ?float
    {
        $crawler = new Crawler($html);
        $crawler = $crawler->filter('div[data-gae-product-price]');

        if($crawler->count() === 0) {
            throw new \Exception('couldnt find div with price data attribute');
        }

        $price = $crawler->attr('data-gae-product-price');
        $price = floatval($price);

        return $price;
    }

}