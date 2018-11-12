<?php
/**
 * Created by PhpStorm.
 * User: babanin
 * Date: 11/12/2018
 * Time: 19:48
 */

class Crud_model extends CI_Model
{
    function __construct()
    {
        $this->load->helper('url');
        $this->load->database('mysql');
    }


    public function getResult($sql, $table){
        $sqlstring = strtolower($sql);
        if (!preg_match('/\b'.$table.'\b/',$sqlstring) ||!preg_match('/\bselect\b/',$sqlstring)) {
            return ['success' => '0','table_name'=>$table];
        }
        $query = $this->db->query($sqlstring);
        $result = $query->result_array();
        $columns = $this->db->list_fields($table);
        return ['success' => '1',
            'rows' => $result,
            'column_names' => $columns,
            'table_name'=>$table,
            'numrows'=>$this->db->count_all($table)];
    }

}