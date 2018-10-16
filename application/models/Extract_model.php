<?php

class Extract_model extends CI_Model{

    function __construct()
    {
        $this->load->helper('url');
        $this->load->helper('extract');
        $this->load->library('categorycrawler');
    }

    public function extractLinks($amountOfPages){
        $errors = array();
        $count = $amountOfPages;
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
        return $this->extractLinks($amountOfPages);


    }


}