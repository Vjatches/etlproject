<?php
use Clue\React\Buzz\Browser;
class Extract_model extends CI_Model{

    function __construct()
    {
        $this->load->helper('url');
        $this->load->helper('extract');

    }

    public function extractLinks($amountOfPages){
        $errors = array();
        $count = $amountOfPages;
        $this->load->library('categorycrawler');
        $maxAmountOfPages = $this->categorycrawler->getAmountOfPages();
        $minAmountOfPages = 1;
        if($count>$maxAmountOfPages || $count < $minAmountOfPages){
            $errors[]="Invalid number of pages. Please specify range between $minAmountOfPages and $maxAmountOfPages";
            return $errors;
        }
        $i = 1;
        $links = array();
        while ($i <= $count) {
            $crawlers = new categorycrawler('' . 'https://allegro.pl/kategoria/laptopy-apple-77915' . '?p=' . $i);
            $links[] = $crawlers->getProductLinksFromPage();
            $i++;
        }

        $links = toSingleArray($links);
        return $links;

    }

    public function runExtractorAsync($amountOfPages){
        $start_time=microtime(1);

        $links =  $this->extractLinks($amountOfPages);

        $loop = React\EventLoop\Factory::create();
        $client = new Browser($loop);

        $this->load->library('scraper');
        $this->scraper->setClient($client);

        $this->scraper->scrape($links);

        $loop->run();

        $result['product']=$this->scraper->getData();
        //Uncomment to get page and check for new class vocabulary
       /* $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile('https://allegro.pl/apple-macbook-air-13-mqd32ze-a-i5-8gb-128ssd-i7477224192.html');
        $crawler = new \Symfony\Component\DomCrawler\Crawler($dom, 'https://allegro.pl/');
        $result['product']=$crawler->html();*/

        $end_time=microtime(1);
        $execution_time=$end_time-$start_time;
        $result['executiontime']=$execution_time;
        return $result;
    }


}