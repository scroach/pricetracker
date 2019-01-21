<?php

namespace App\Service\Crawler;


class Hornbach implements PriceCrawler
{

    public function extractPrice(string $html): ?float
    {
        preg_match('/"price":"([\d,.]+)"/', $html, $matches);

        if(!isset($matches[1])) {
            throw new \Exception('couldnt find div with price data attribute');
        }

        $price = $matches[1];
        $price = floatval($price);

        return $price;
    }

}