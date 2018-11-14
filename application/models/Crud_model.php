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

}