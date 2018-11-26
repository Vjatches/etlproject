<?php

use Clue\React\Buzz\Browser;
require_once 'Worm.php';

/**
 * Class Scraper
 * Class which creates 'virtual' browsers which open pages and scrap data from them
 */
class Scraper
{
    /**
     * Object of current browser client
     * @var
     */
    private $client;

    /**
     * Array of scrapped data
     * @var array
     */
    private $scraped = [];

    /**
     * Setter which allows to set current client
     * @param Browser $client
     */
    public function setClient(Browser $client){
        $this->client = $client;
    }


    /**
     * Main scrape function which scraps provided url's in a multi-threading manner using a queue
     * @param array $urls - array of urls to be scrapped
     * @param $concurrencyLimit - queue limit
     */
    public function scrape(array $urls = [], $concurrencyLimit)
    {
        //Create new queue with provided concurency limit
        $queue = new Clue\React\Mq\Queue($concurrencyLimit, null, function ($url) {
            //When client is in queue extract content from url
            return $this->client->get($url);
        });
        $this->scraped = [];

        foreach ($urls as $url) {
            //Populate queue with links
           $queue($url)->then(
               //When queue processes link invoke next function
                function (\Psr\Http\Message\ResponseInterface $response) {
                    //Extract data from html and write result to scrapped array
                    $this->scraped[] = $this->extractFromHtml((string) $response->getBody());
                });
        }
    }

    /**
     * Function which is used to extract data from html document
     * @param $html - html document
     * @return mixed - array with data
     */
    public function extractFromHtml($html)
    {
        //Create new Worm and send it to html document
        $crawler = new Worm($html);
        $item = $crawler->getJson();
       return $item;
    }

    /**
     * Getter to return scrapped data
     * @return array - scraped data
     */
    public function getData()
    {
        return $this->scraped;
    }
}