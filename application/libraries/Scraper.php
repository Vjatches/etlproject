<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 10/16/2018
 * Time: 22:58
 */
use Clue\React\Buzz\Browser;
require_once 'Worm.php';
class Scraper
{
    private $client;

    private $scraped = [];

    public function setClient(Browser $client){
        $this->client = $client;
    }


    public function scrape(array $urls = [], $concurrencyLimit)
    {
        $queue = new Clue\React\Mq\Queue($concurrencyLimit, null, function ($url) {
            return $this->client->get($url);
        });
        $this->scraped = [];

        foreach ($urls as $url) {
           $queue($url)->then(
                function (\Psr\Http\Message\ResponseInterface $response) {
                    $this->scraped[] = $this->extractFromHtml((string) $response->getBody());
                });
        }
    }

    public function extractFromHtml($html)
    {
        $crawler = new Worm($html);
        $item = $crawler->getJson();
       return $item;
    }

    public function getData()
    {
        return $this->scraped;
    }
}