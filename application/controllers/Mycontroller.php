<?php

class Mycontroller extends CI_Controller{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('html');
		$this->load->helper('url');
		$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	}


	public function home(){
		$data['current'] = 'home';
		$this->load->view('templates/meta');
		$this->load->view('templates/sidebar', $data);
		$this->load->view('templates/topbar',$data);
		$this->load->view('pages/home');
		$this->load->view('templates/footer');
		$this->load->view('templates/script');


	}
	public function page1(){
		$data['current'] = 'page1';

		$this->load->view('templates/meta');
		$this->load->view('templates/sidebar', $data);
		$this->load->view('templates/topbar',$data);
		$this->load->view('templates/footer');
		$this->load->view('templates/script');


	}


    public function page2(){
        $data['current'] = 'page2';

        $this->load->view('templates/meta');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar',$data);
        $this->load->view('templates/footer');
        $this->load->view('templates/script');


    }








}

?>
