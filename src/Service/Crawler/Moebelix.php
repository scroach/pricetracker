<?php

namespace App\Service\Crawler;


use Symfony\Component\DomCrawler\Crawler;

class Moebelix implements PriceCrawler
{

    public function extractPrice(string $html): ?float
    {
        $crawler = new Crawler($html);
        $crawler = $crawler->filter('meta[itemprop="price"]');

        if($crawler->count() === 0) {
            throw new \Exception('couldnt find meta tag with price');
        }

        $price = $crawler->attr('content');
        $price = floatval($price);

        return $price;
    }

}