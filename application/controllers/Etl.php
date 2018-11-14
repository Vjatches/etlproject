<?php

class Etl extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('html');
        $this->load->helper('url');
        $this->load->helper('extract');
        $this->load->helper('transform');
        $this->load->model('extract_model');
        $this->load->model('transform_model');
        $this->load->model('load_model');
        $this->load->model('crud_model');
        $this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
        ('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->output->set_header('Pragma: no-cache');
        $this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    }

    public function home()
    {
        $data['current'] = 'home';
        $data['toccurrent'] = 'home';
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $data['pagesqty'] = $this->extract_model->getPagesQuantity();
        $data['choice'] = $this->transform_model->getChoice();
        $data['checkboxes'] = generateCheckboxes();
        $data['rowsqty'] = $this->load_model->getRowsQuantity();

        $this->form_validation->set_rules('amountOfPages', 'Amount of pages', 'required|callback_quantity_check', array('required' => 'Please, provide amount of pages to extract'));
        $this->form_validation->set_rules('numrows', 'Number of rows', 'required|callback_rows_check', array('required' => 'Please, provide amount of rows to load'));
        $this->form_validation->set_rules('fields[]', 'Fields', 'required');
        $this->form_validation->set_message('required', 'Please, choose at least one attribute');

        if ($this->form_validation->run($this) === FALSE) {
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/home', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        }else{

            $data['content']['extract'] = $this->extract_model->runExtractorAsync($this->input->post('amountOfPages'));
            $data['content']['transform'] = $this->transform_model->runTransform($this->input->post('fields[]'));
            $data['content']['load'] = $this->load_model->runLoad($this->input->post('numrows'),'etl_module');
            $data['content']['cleanup'] = $this->crud_model->cleanUp($this->input->post('cleanups[]'));
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/home_result', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        }


    }

    public function extract()
    {


        $data['current'] = 'extract';
        $data['toccurrent'] = '';

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $data['pagesqty'] = $this->extract_model->getPagesQuantity();

        $this->form_validation->set_rules('amountOfPages', 'Amount of pages', 'required|callback_quantity_check', array('required' => 'Please, provide amount of pages to extract'));
        if ($this->form_validation->run($this) === FALSE) {

            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/extract_app', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        } else {

            $data['content'] = $this->extract_model->runExtractorAsync($this->input->post('amountOfPages'));
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/extract_result', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');

        }


    }

    public function transform()
    {
        $data['current'] = 'transform';
        $data['toccurrent'] = '';
        $data['checkboxes'] = generateCheckboxes();
        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('fields[]', 'Fields', 'required');
        $this->form_validation->set_message('required', 'Please, choose at least one attribute');

        if ($this->form_validation->run() === FALSE) {
            $data['choice'] = $this->transform_model->getChoice();
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/transform_app');
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        } else {
            $this->transform_model->setChoice($this->input->post('fields[]'), $this->input->post('default_chb'));
            $data['content'] = $this->transform_model->runTransform($this->input->post('fields[]'));
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/transform_result', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');

        }

    }

    public function load()
    {
        $data['current'] = 'load';
        $data['toccurrent'] = '';
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $data['rowsqty'] = $this->load_model->getRowsQuantity();

        $this->form_validation->set_rules('numrows', 'Number of rows', 'required|callback_rows_check', array('required' => 'Please, provide amount of rows to load'));
        if ($this->form_validation->run($this) === FALSE) {

            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/load_app', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        } else {
            $data['content'] = $this->load_model->runLoad($this->input->post('numrows'),'load_module');
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/load_result', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        }

    }

    public function crudhome()
    {
        $data['current'] = 'crudhome';
        $data['toccurrent'] = '';
        $this->load->view('templates/meta');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('pages/crud/crudhome', $data);
        $this->load->view('templates/footer');
        $this->load->view('templates/script');
    }

    public function emongocrud()
    {
        $data['current'] = 'crudhome';
        $data['toccurrent'] = 'emongocrud';
        $this->load->view('templates/meta');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('pages/crud/mongocrud', $data);
        $this->load->view('templates/footer');
        $this->load->view('templates/script');
    }

    public function tmongocrud()
    {
        $data['current'] = 'crudhome';
        $data['toccurrent'] = 'tmongocrud';
        $this->load->view('templates/meta');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('pages/crud/mongocrud', $data);
        $this->load->view('templates/footer');
        $this->load->view('templates/script');
    }

    public function tsqlcrud()
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules('query', 'Query', 'required', array('required' => 'Please, provide query'));
        if ($this->form_validation->run($this) === FALSE) {
            $data['current'] = 'crudhome';
            $data['toccurrent'] = 'tsqlcrud';
            $data['content'] =$this->crud_model->getResult('select * from temp_products','temp_products');
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/crud/sqlcrud', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        } else {

            $data['current'] = 'crudhome';
            $data['toccurrent'] = 'tsqlcrud';
            $data['content'] = $this->crud_model->getResult($this->input->post('query'),'temp_products');
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/crud/sqlcrud', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        }
    }

    public function lsqlcrud()
    {

        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules('query', 'Query', 'required', array('required' => 'Please, provide query'));
        if ($this->form_validation->run($this) === FALSE) {
            $data['current'] = 'crudhome';
            $data['toccurrent'] = 'lsqlcrud';
            $data['content'] =$this->crud_model->getResult('select * from products','products');
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/crud/sqlcrud', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        } else {

            $data['current'] = 'crudhome';
            $data['toccurrent'] = 'lsqlcrud';
            $data['content'] = $this->crud_model->getResult($this->input->post('query'),'products');
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/crud/sqlcrud', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        }
    }

    public function extractPage()
    {
        $data['current'] = 'extractPage';
        $data['toccurrent'] = '';
        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');

        $this->form_validation->set_rules('pageUrl', 'PageUrl', 'required');
        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/pageapp');
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        } else {


            $data['content'] = $this->extract_model->getPageWithItem($this->input->post('pageUrl'));

            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/pageresult', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');

        }


    }


    public function quantity_check($input)
    {
        $max = $this->extract_model->getPagesQuantity();
        if ($input > $max) {
            $this->form_validation->set_message('quantity_check', '{field} can not be bigger than <b>' . $max . '</b>');
            return FALSE;
        } elseif ($input < 1) {
            $this->form_validation->set_message('quantity_check', '{field} can not be smaller than <b>1</b>');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function rows_check($input)
    {
        $max = $this->load_model->getRowsQuantity();
        if ($input > $max) {
            $this->form_validation->set_message('rows_check', '{field} can not be bigger than <b>' . $max . '</b>');
            return FALSE;
        } elseif ($input < 0) {
            $this->form_validation->set_message('rows_check', '{field} can not be smaller than <b>0</b>');
            return FALSE;
        } else {
            return TRUE;
        }
    }


}

?>
