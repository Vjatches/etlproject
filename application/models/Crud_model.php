<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 11/12/2018
 * Time: 19:48
 */

class Crud_model extends CI_Model
{
    private $mongoClient;
    function __construct()
    {
        $this->mongoClient= new \MongoDB\Client('mongodb+srv://root:root@kreslav-hcr9i.mongodb.net/test?retryWrites=true');
        $this->load->helper('url');
        $this->load->helper('crud');
        $this->load->database('mysql');
    }

    public function get_collection($input_collection, $input_filter){
        $filter = json_decode($input_filter);

        $collection = $this->mongoClient->extracthub->$input_collection;
        $cursor = $collection->find($filter, ['limit'=>20]);
        $result = [];
        foreach ($cursor as $id=>$document){
            $result[$id] = json_encode($document);
        }

        return ['documents'=>$result, 'num_documents' => $collection->count($filter), 'table_name' =>$input_collection, 'filter'=>$input_filter];
    }

    public function getResult($sql, $table){

        $sqlstring = validateSql($sql);

        if (!preg_match('/\b'.$table.'\b/',$sqlstring) ||!preg_match('/\bselect\b/',$sqlstring)) {
            return ['success' => '0','table_name'=>$table, 'query'=>$sqlstring, 'error'=>['code'=>'1','message'=>'Please provide valid SELECT query string to table '.$table]];
        }

        $query = $this->db->query($sqlstring);
        if( $query !== FALSE ){
            $querystring = str_replace('limit 100','',$this->db->last_query());
            $result = $query->result_array();
            $columns = $query->list_fields();

            $numrows = $this->db->query('select count(*) '.strstr($sqlstring,'from'))->result_array();

            return ['success' => '1',
                'rows' => $result,
                'column_names' => $columns,
                'table_name'=>$table,
                'numrows'=>$numrows[0]['count(*)'],
                'query'=>$querystring];
        }
        return ['success' => '0','table_name'=>$table, 'error'=> $this->db->error()];

    }

    public function cleanUp($input){
        $result = [];
        if(!isset($input) or $input == NULL){
            return $result;
        }
        foreach ($input as $datastructure){
            if(getDbType($datastructure) =='sql'){
                $datastructure = str_replace('sql_', '',$datastructure);
                if($this->db->query('DELETE FROM '.$datastructure) !== FALSE){
                    $result['clean_succ'][] = $datastructure;
                }else{
                    $result['clean_fail'][] = $datastructure;
                }
            }else{
                $collection = $this->mongoClient->extracthub->$datastructure;
                $collection->deleteMany([]);
                $result['clean_succ'][] = $datastructure;
            }

        }
        return $result;
    }

    public function get_phase(){
        $sql = 'select state from phase';
        $query = $this->db->query($sql);
        if($query === FALSE){
            return $this->db->error();
            }
        $result = $query->result_array();
        $currentphase = $result[0]['state'];
        return $currentphase;
    }

    public function set_phase($phase){
        $sql = 'update phase set state = \''.$phase.'\';';
        $query = $this->db->query($sql);
        if($query === FALSE){
            return $this->db->error();
        }
        return true;
    }

    public function get_settings(){
        $sql = 'select * from settings';
        $query = $this->db->query($sql);
        if($query === FALSE){
            return $this->db->error();
        }
        $result = $query->result_array();
        $category = $result[0]['category'];
        $restriction = $result[0]['restriction_level'];

        return ['category'=>''.$category,'restriction_level'=>$restriction];
    }

    public function set_settings(array $settings){
        $sql = 'update settings set category = \''.$settings['category'].'\', restriction_level = \''.$settings['restriction_level'].'\';';
        $query = $this->db->query($sql);
        if($query === FALSE){
            return $this->db->error();
        }
        return true;
    }

    public function check_restrictions($page, $phase){
        $settings = $this->get_settings();
        $restriction_level = $settings['restriction_level'];

        if ($restriction_level == 'strict'){
            return $page == $phase;
        }elseif ($restriction_level == 'development'){
            return true;
        }elseif ($restriction_level == 'soft'){

            switch($phase){
                case 'extract':
                    if($page == 'extract' || $page == 'load'){
                        return true;
                    }else{
                        return false;
                    }
                case 'transform':
                    if($page == 'transform' || $page == 'extract'){
                        return true;
                    }else{
                        return false;
                    }
                case 'load':
                    if($page == 'load' || $page == 'transform'){
                        return true;
                    }else{
                        return false;
                    }
                default:
                    return ['error'=>'phase is not valid, check database'];
            }

        }
        return ['error'=>'restriction_level is not valid, check database'];
    }

    public function get_csv($sql_query){
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        $query = $this->db->query($sql_query);
        $delimiter = ",";
        $newline = "\r\n";
        $data = $this->dbutil->csv_from_result($query, $delimiter, $newline);
        force_download('CSV_Report.csv', $data);
    }
}