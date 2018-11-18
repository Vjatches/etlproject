<?php

use Clue\React\Buzz\Browser;

class Extract_model extends CI_Model
{
    private $settings;

    function __construct()
    {

        $this->load->model('crud_model');
        $this->load->helper('url');
        $this->load->helper('extract');
        $this->settings = $this->crud_model->get_settings();
    }

    public function getPagesQuantity()
    {

        $category[] = $this->settings['category'];
        $this->load->library('categorycrawler', $category);
        $amountOfPages = $this->categorycrawler->getAmountOfPages();
        return $amountOfPages;
    }


    public function extractLinks($amountOfPages)
    {
        $category = $this->settings['category'];

        $count = $amountOfPages;
        $i = 1;
        $links = array();
        while ($i <= $count) {
            $category_page[0] = $category.'?p='.$i;
            $crawlers = new categorycrawler($category_page);
            $links[] = $crawlers->getProductLinksFromPage();
            $i++;
        }

        $links = toSingleArray($links);
        return $links;

    }

    public function runExtractorAsync($amountOfPages = 1)
    {
        $this->load->library('timer');
        $this->timer->start();
        set_time_limit(0);

        $links = $this->extractLinks($amountOfPages);

        $loop = React\EventLoop\Factory::create();
        $client = new Browser($loop);

        $this->load->library('scraper');
        $this->scraper->setClient($client);

        $this->scraper->scrape($links, 10);

        $loop->run();

        //database insert
        $products = $this->scraper->getData();
        $query = $this->loadToDatabase($products);


        $result['amount']['parsed'] = count($links);
        $result['amount']['affected'] = $query['inserted'];
        $result['amount']['notaffected'] = $query['matched'];
        $result['executiontime'] = $this->timer->stop();
        //Explanation 'associative array'
        /*$result = ['executiontime'=>22,
            'amount' => ['parsed' => 64, 'affected' => 0, 'notaffected' => 64],
            'test' => ['test', ['level'=>1]]
        ];*/
        return $result;
    }

    public function loadToDatabase(array $array)
    {
        $bulk = new \MongoDB\Driver\BulkWrite(['ordered' => false]);
        foreach ($array as $item) {
            $bulk->update(
                ['_id' => $item['notifyAndWatch']['offerId']],
                array('$setOnInsert' => $item),
                array('upsert' => true)
            );
        }

        $manager = new \MongoDB\Driver\Manager('mongodb+srv://root:root@kreslav-hcr9i.mongodb.net');
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 100);

        $insert_result = $manager->executeBulkWrite('extracthub.products', $bulk, $writeConcern);
        $amountofinserted = $insert_result->getUpsertedCount();
        return [
            'inserted' => $insert_result->getUpsertedCount(),
            'matched' => $insert_result->getMatchedCount()
        ];
    }

    public function getPageWithItem($url)
    {
        $this->load->library('timer');
        $this->timer->start();
        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($url);
        $crawler = new \Symfony\Component\DomCrawler\Crawler($dom, 'https://allegro.pl/');
        //$result['product']=$crawler->html();
        $text = $crawler->filter('[data-box-name="summary"]')->filter('script')->html();
        $start = '"primarySlot":';
        $end = '"additionalServices"';
        $jsonstring = get_string_between($text, $start, $end);
        $stripped = str_replace($jsonstring, "{},", $text);

        $json = rtrim(strstr($stripped, "{\""), ";");
        //$item[] = json_decode($json,true);
        $result['product'] = $json;

        $result['executiontime'] = $this->timer->stop();
        return $result;
    }


}
