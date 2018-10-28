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

    public function runExtractorAsync($amountOfPages,$concurrent){
    	set_time_limit(0);
        $start_time=microtime(1);

        $links =  $this->extractLinks($amountOfPages);

        $loop = React\EventLoop\Factory::create();
        $client = new Browser($loop);

        $this->load->library('scraper');
        $this->scraper->setClient($client);

        $this->scraper->scrape($links,$concurrent);

        $loop->run();

        //Uncomment to get page and check for new class vocabulary
        /*$dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile('https://allegro.pl/apple-macbook-pro-15-retina-256-ssd-ms-office-i7623507422.html');
		@$dom->loadHTMLFile('https://allegro.pl/apple-macbook-pro-15-i7-2-3ghz-8gb-256gb-a1398-i7621793023.html');
        $crawler = new \Symfony\Component\DomCrawler\Crawler($dom, 'https://allegro.pl/');
        $result['product']=$crawler->html();*/
//$result['product']=$this->scraper->getData();

        //database insert
        $products = $this->scraper->getData();
        $query = $this->loadToDatabase($products);


        $end_time=microtime(1);
        $execution_time=$end_time-$start_time;

        $result['executiontime']=$execution_time;
        $result['amount']['parsed']=count($links);
        $result['amount']['affected']=$query['inserted'];
        $result['amount']['notaffected']=$query['matched'];
        return $result;
    }

    public function loadToDatabase(array $array){
        $bulk = new \MongoDB\Driver\BulkWrite(['ordered'=>false]);
        foreach ($array as $item){
            $bulk->update(
                ['_id' =>$item['notifyAndWatch']['offerId']],
                array('$setOnInsert' => $item),
                array('upsert' => true)
            );
        }

        $manager = new \MongoDB\Driver\Manager('mongodb+srv://root:root@kreslav-hcr9i.mongodb.net');
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 100);

        $insert_result = $manager->executeBulkWrite('extracthub.products', $bulk, $writeConcern);
        $amountofinserted = $insert_result->getUpsertedCount();
        return [
            'inserted'=>$insert_result->getUpsertedCount(),
            'matched'=>$insert_result->getMatchedCount()
        ];
    }

    public function getPageWithItem($url){
        $start_time=microtime(1);
        $dom = new DOMDocument('1.0');
        @$dom->loadHTMLFile($url);
        $crawler = new \Symfony\Component\DomCrawler\Crawler($dom, 'https://allegro.pl/');
        //$result['product']=$crawler->html();
        $text = $crawler->filter('[data-box-name="summary"]')->filter('script')->html();
        $start = '"primarySlot":';
        $end = '"additionalServices"';
        $jsonstring = get_string_between($text,$start,$end);
        $stripped = str_replace($jsonstring, "{},",$text);

        $json = rtrim(strstr($stripped,"{\""),";");
        $item[] = json_decode($json,true);
        $result['product']=$item;
        $end_time=microtime(1);
        $execution_time=$end_time-$start_time;
        $result['executiontime']=$execution_time;
        return $result;
    }


}
