<?php

namespace App\Service\Crawler;


use Symfony\Component\DomCrawler\Crawler;

class Obi implements PriceCrawler
{

    public function extractPrice(string $html): ?float
    {
        $crawler = new Crawler($html);
        $crawler = $crawler->filter('strong[itemprop="price"]');

        if($crawler->count() === 0) {
            throw new \Exception('couldnt find strong tag with price');
        }

        $price = $crawler->text();
        $price = floatval(str_replace(',', '.', str_replace('.', '', $price)));

        return $price;
    }

}