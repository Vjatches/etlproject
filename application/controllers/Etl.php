<?php

class Etl extends CI_Controller{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('html');
		$this->load->helper('url');
		$this->load->helper('extract');
		$this->load->model('extract_model');
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
	public function extract(){
		$data['current'] = 'extract';
        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');

        $this->form_validation->set_rules('amountOfPages', 'AmountOfPages', 'required');
        if ($this->form_validation->run() === FALSE){
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar',$data);
            $this->load->view('pages/extractapp');
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
		}else{


			$data['content']=$this->extract_model->runExtractorAsync($this->input->post('amountOfPages'),$this->input->post('concurrent'));

            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar',$data);
            $this->load->view('pages/extractresult',$data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');

		}




	}

    public function extractPage(){
        $data['current'] = 'extractPage';
        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');

        $this->form_validation->set_rules('pageUrl', 'PageUrl', 'required');
        if ($this->form_validation->run() === FALSE){
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar',$data);
            $this->load->view('pages/pageapp');
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        }else{


            $data['content']=$this->extract_model->getPageWithItem($this->input->post('pageUrl'));

            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar',$data);
            $this->load->view('pages/pageresult',$data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');

        }




    }


    public function transform(){
        $data['current'] = 'transform';

        $this->load->view('templates/meta');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar',$data);
        $this->load->view('templates/footer');
        $this->load->view('templates/script');


    }
    public function load(){
        $data['current'] = 'load';

        $this->load->view('templates/meta');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar',$data);
        $this->load->view('templates/footer');
        $this->load->view('templates/script');


    }









}

?>
