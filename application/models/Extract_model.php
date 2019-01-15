<?php

use Clue\React\Buzz\Browser;

/**
 * Class Extract_model
 * Main model for the Extract process. It handles all Extract back-end logic
 */
class Extract_model extends CI_Model
{
    /**
     * Variable which contains global application settings
     * @var
     */
    private $settings;

    /**
     * Extract_model constructor.
     * Loads helpers, other models, libraries and settings
     */
    function __construct()
    {
        //Load crud model so we can read settings from database
        $this->load->model('crud_model');
        //Load settings from database via crud_model
        $this->settings = $this->crud_model->get_settings();

        //Load helpers with usefull functions
        $this->load->helper('url'); //built-in
        $this->load->helper('extract'); //custom
    }

    /**
     * Function which is used to get pages quantity from category
     * @return mixed - amount of pages
     */
    public function getPagesQuantity()
    {
        //Get category from settings
        $category[] = $this->settings['category'];
        //Load category crawler
        $this->load->library('categorycrawler', $category);
        //Send crawler to get amount of pages from current working category
        $amountOfPages = $this->categorycrawler->getAmountOfPages();
        return $amountOfPages;
    }


    /**
     * Function which extracts links to products from current category
     * @param $amountOfPages - from how many pages we want to extract links
     * @return array - array with product links
     */
    public function extractLinks($amountOfPages)
    {
        //Set from how many pages from category we wont to extract links
        $count = $amountOfPages;
        $i = 1;
        //Empty container which will be populated with links
        $links = array();
        //Extract links from category pages in a loop while i is smaller that requested amount of pages
        while ($i <= $count) {
            //Create link to new category page which looks like https://allegro.pl/category?p=i where ?p=i part shows page from which we will extract links
            $category_page[0] = $this->settings['category'].'?p='.$i;
            //Create category crawler and send it to collect links from this page
            $crawlers = new categorycrawler($category_page);
            //Populate $links array with links which category crawler brought to us
            $links[] = $crawlers->getProductLinksFromPage();
            //Increase value of i
            $i++;
        }
        //Aggregate links to one-dimesional array for further ease of processing
        $links = toSingleArray($links);
        return $links;

    }

    /**
     * Main function of Extract process which uses all other functions
     * @param int $amountOfPages - amount of pages to extract from allegro category
     * @return mixed - returns report with data about extract process
     */
    public function runExtractorAsync($amountOfPages = 1)
    {
        //Load timer library
        $this->load->library('timer');
        //Start timer
        $this->timer->start();
        //Set php timeout to 0 so it will not timeout during lengthy scraping process
        set_time_limit(0);
        //Extract links from allegro category pages
        $links = $this->extractLinks($amountOfPages);
        //Create multi-threading loop
        $loop = React\EventLoop\Factory::create();
        //Create virtual browser which will process pages
        $client = new Browser($loop);
        //Load scraper class which will scrape data from products
        $this->load->library('scraper');
        //Set browser client for scraper
        $this->scraper->setClient($client);
        //Set data on which scraper will operate. Links is an array of product links from allegro category, 10 is an amount of simultaneous request
        $this->scraper->scrape($links, 10);
        //Start multi-threading loop
        $loop->run();
        //After loop finished looping, retrieve data from scraper
        $products = $this->scraper->getData();
        //Insert data to database
        $query = $this->loadToDatabase($products);
        //Pack data to be sent as a report of a process
        $result = [
            'amount' => [
                'parsed' => count($links),
                'affected' => $query['inserted'],
                'notaffected' => $query['matched'],
                ],
            'executiontime' =>  $this->timer->stop()
        ];

        return $result;
    }

    /**
     * Function which loads extracted data to mongo database
     * @param array $array - array of extracted data
     * @return array - report of load process
     */
    public function loadToDatabase(array $array)
    {
        //Create a bulk object
        $bulk = new \MongoDB\Driver\BulkWrite(['ordered' => false]);
        //Populate bulk object with records to be inserted in database
        foreach ($array as $item) {
            $bulk->update(
                ['_id' => $item['notifyAndWatch']['offerId']],
                array('$setOnInsert' => $item),
                array('upsert' => true)
            );
        }
        //Create manager which will perform bulk write to a certain mongodb database. Provide credentials to database as parameter
        $manager = new \MongoDB\Driver\Manager('mongodb+srv://root:root@kreslav-hcr9i.mongodb.net');
        //Create error handler, if something stucks it will throw an appropriate error
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 100);
        //Let manager execute bulk insert
        $insert_result = $manager->executeBulkWrite('extracthub.products', $bulk, $writeConcern);
        //Pack data and report how many records has been inserted and how many were already present in database
        return [
            'inserted' => $insert_result->getUpsertedCount(),
            'matched' => $insert_result->getMatchedCount()
        ];
    }
}
