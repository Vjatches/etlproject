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
        } else {

            $data['content']['extract'] = $this->extract_model->runExtractorAsync($this->input->post('amountOfPages'));
            $data['content']['transform'] = $this->transform_model->runTransform($this->input->post('fields[]'));
            $data['content']['load'] = $this->load_model->runLoad($this->input->post('numrows'), 'etl_module');
            $data['content']['cleanup'] = $this->crud_model->cleanUp($this->input->post('cleanups[]'));
            $this->load->view('templates/meta');
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('pages/home_result', $data);
            $this->load->view('templates/footer');
            $this->load->view('templates/script');
        }


    }

    private function load_page($page, $data)
    {
        $this->load->view('templates/meta');
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view($page, $data);
        $this->load->view('templates/footer');
        $this->load->view('templates/script');
    }

    public function extract()
    {

        $phase = $this->crud_model->get_phase();
        $data['current'] = 'extract';
        $data['toccurrent'] = '';
        $data['phase'] = $phase;
        if( $phase != $data['current']){
            $this->load_page('pages/wrongphase',$data);
        }else {


            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');

            $data['pagesqty'] = $this->extract_model->getPagesQuantity();

            $this->form_validation->set_rules('amountOfPages', 'Amount of pages', 'required|callback_quantity_check', array('required' => 'Please, provide amount of pages to extract'));
            if ($this->form_validation->run($this) === FALSE) {
                $this->load_page('pages/extract_app', $data);
            } else {
                $data['content'] = $this->extract_model->runExtractorAsync($this->input->post('amountOfPages'));
                $this->crud_model->set_phase('transform');
                $this->load_page('pages/extract_result', $data);
            }

        }

    }

    public function transform()
    {
        $phase = $this->crud_model->get_phase();
        $data['phase'] = $phase;
        $data['current'] = 'transform';
        $data['toccurrent'] = '';
        if( $phase != $data['current']){
            $this->load_page('pages/wrongphase',$data);
        }else {

            $data['checkboxes'] = generateCheckboxes();
            $this->load->helper(array('form', 'url'));

            $this->load->library('form_validation');
            $this->form_validation->set_rules('fields[]', 'Fields', 'required');
            $this->form_validation->set_message('required', 'Please, choose at least one attribute');

            if ($this->form_validation->run() === FALSE) {
                $data['choice'] = $this->transform_model->getChoice();
                $this->load_page('pages/transform_app', $data);
            } else {
                $this->transform_model->setChoice($this->input->post('fields[]'), $this->input->post('default_chb'));
                $data['content'] = $this->transform_model->runTransform($this->input->post('fields[]'));
                $this->crud_model->set_phase('load');

                $this->load_page('pages/transform_result', $data);
            }
        }

    }

    public function load()
    {
        $phase = $this->crud_model->get_phase();
        $data['phase'] = $phase;
        $data['current'] = 'load';
        $data['toccurrent'] = '';
        if( $phase != $data['current']){
            $this->load_page('pages/wrongphase',$data);
        }else {

            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');
            $data['rowsqty'] = $this->load_model->getRowsQuantity();

            $this->form_validation->set_rules('numrows', 'Number of rows', 'required|callback_rows_check', array('required' => 'Please, provide amount of rows to load'));
            if ($this->form_validation->run($this) === FALSE) {
                $this->load_page('pages/load_app', $data);
            } else {
                $data['content'] = $this->load_model->runLoad($this->input->post('numrows'), 'load_module');
                $this->crud_model->set_phase('extract');
                $this->load_page('pages/load_result', $data);
            }
        }

    }

    public function crudhome()
    {
        $data['current'] = 'crudhome';
        $data['toccurrent'] = '';
        $this->load_page('pages/crud/crudhome', $data);
    }

    public function emongocrud()
    {
        $data['current'] = 'crudhome';
        $data['toccurrent'] = 'emongocrud';
        $this->load_page('pages/crud/mongocrud', $data);
    }

    public function tmongocrud()
    {
        $data['current'] = 'crudhome';
        $data['toccurrent'] = 'tmongocrud';
        $this->load_page('pages/crud/mongocrud', $data);
    }

    public function tsqlcrud()
    {
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules('query', 'Query', 'required', array('required' => 'Please, provide query'));
        if ($this->form_validation->run($this) === FALSE) {
            $data['current'] = 'crudhome';
            $data['toccurrent'] = 'tsqlcrud';
            $data['content'] = $this->crud_model->getResult('select * from temp_products', 'temp_products');
            $this->load_page('pages/crud/sqlcrud', $data);
        } else {

            $data['current'] = 'crudhome';
            $data['toccurrent'] = 'tsqlcrud';
            $data['content'] = $this->crud_model->getResult($this->input->post('query'), 'temp_products');
            $this->load_page('pages/crud/sqlcrud', $data);
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
            $data['content'] = $this->crud_model->getResult('select * from products', 'products');
            $this->load_page('pages/crud/sqlcrud', $data);
        } else {

            $data['current'] = 'crudhome';
            $data['toccurrent'] = 'lsqlcrud';
            $data['content'] = $this->crud_model->getResult($this->input->post('query'), 'products');
            $this->load_page('pages/crud/sqlcrud', $data);
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
            $this->load_page('pages/pageapp', $data);
        } else {
            $data['content'] = $this->extract_model->getPageWithItem($this->input->post('pageUrl'));

            $$this->load_page('pages/pageresult', $data);

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
