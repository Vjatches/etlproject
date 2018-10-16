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



    public function runExtractor($amountOfPages){
        $start_time=microtime(1);
        $links =  $this->extractLinks($amountOfPages);
        //$links[]='https://allegro.pl/macbook-pro-15-2018-i9-32gb-512gb-560x-4gb-space-i7464414615.html';
        $initialParams = $links[0];
        $this->load->library('itemcrawler', $initialParams);

        foreach ($links as $link){
            $crawler = new itemcrawler($link);
            $result['product'][] = array(
                'title' => $crawler->getAttribute(getClassSelector('title')),
                'price' => $crawler->getAttribute(getClassSelector('price')),
                'seller' =>$crawler->getAttribute(getClassSelector('seller'))
            );
        }
        $end_time=microtime(1);
        $execution_time=$end_time-$start_time;
        $result['executiontime']=$execution_time;
        return $result;


    }

    public function runExtractorAsync($amountOfPages){
        $start_time=microtime(1);

        $links =  $this->extractLinks($amountOfPages);

        $loop = React\EventLoop\Factory::create();
        $client = new Browser($loop);

        //$scraper = new Scraper($client);
        $this->load->library('scraper');
        $this->scraper->setClient($client);

        $this->scraper->scrape($links);

        $loop->run();
        $result['product']=$this->scraper->getData();
        $end_time=microtime(1);
        $execution_time=$end_time-$start_time;
        $result['executiontime']=$execution_time;
        return $result;
    }


}