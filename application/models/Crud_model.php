<?php


/**
 * Class Crud_model
 * Main model for the CRUD process. It handles all Create Read Update Delete database back-end logic
 */
class Crud_model extends CI_Model
{
    /**
     * ariable which holds object responsible for connection with MongoDB
     * @var \MongoDB\Client
     */
    private $mongoClient;

    /**
     * Crud_model constructor.
     * Here we connect to databases and load helpers
     */
    function __construct()
    {
        //connect to MongoDB
        $this->mongoClient= new \MongoDB\Client('mongodb+srv://root:root@kreslav-hcr9i.mongodb.net/test?retryWrites=true');
        //Load helpers
        $this->load->helper('url');
        $this->load->helper('crud');
        //connect to mysql, connection parameters are in application/config/database.php
        $this->load->database('mysql');
    }

    /**
     * Function which allows us to perform filter on mongoDB collection
     * @param $input_collection - name of collection
     * @param $input_filter - valid json filter
     * @return array - query result
     */
    public function get_collection($input_collection, $input_filter){
        //Decode json filter to string
        $filter = json_decode($input_filter);
        //Assign collection to variable
        $collection = $this->mongoClient->extracthub->$input_collection;
        //Perform find() operation on collection with given filer and return result as cursor object. Default limit for amount of returned results is 20
        $cursor = $collection->find($filter, ['limit'=>20]);
        //Initiate result container
        $result = [];
        //Loop through cursor object and convert BSON cursor to php array
        foreach ($cursor as $id=>$document){
            $result[$id] = json_encode($document);
        }
        //Pack array as well as amount of documents and name of a collection and return it
        return ['documents'=>$result, 'num_documents' => $collection->count($filter), 'table_name' =>$input_collection, 'filter'=>$input_filter];
    }

    /**
     * Function which allows us to perform query on sql table
     * @param $sql - query string
     * @param $table - table name
     * @return array - query result
     */
    public function getResult($sql, $table){
        //Validate sql string
        $sqlstring = validateSql($sql);
        //If string doesnt contain word Select or name of the current table return error
        if (!preg_match('/\b'.$table.'\b/',$sqlstring) ||!preg_match('/\bselect\b/',$sqlstring)) {
            return ['success' => '0','table_name'=>$table, 'query'=>$sqlstring, 'error'=>['code'=>'1','message'=>'Please provide valid SELECT query string to table '.$table]];
        }
        //Run query with provided sql string on a provided table
        $query = $this->db->query($sqlstring);
        //If query succeded
        if( $query !== FALSE ){
            //trim last query string from limit keyword
            $querystring = str_replace('limit 100','',$this->db->last_query());
            //assign result to an array
            $result = $query->result_array();
            //assign names of columns in table to variable
            $columns = $query->list_fields();
            //get total amount of rows which satisfy last query
            $numrows = $this->db->query('select count(*) '.strstr($sqlstring,'from'))->result_array();
            //pack data report and return it
            return ['success' => '1',
                'rows' => $result,
                'column_names' => $columns,
                'table_name'=>$table,
                'numrows'=>$numrows[0]['count(*)'],
                'query'=>$querystring];
        }
        //If query failed return error
        return ['success' => '0','table_name'=>$table, 'error'=> $this->db->error()];

    }

    /**
     * Function which allows delete everything from table or collection
     * @param $input - array of tables/collection to clean up
     * @return array - report which contains which data structures cleaned up successfully and which failed
     */
    public function cleanUp($input){
        //Prepare container to store result
        $result = [];
        //if input is empty return empty result
        if(!isset($input) or $input == NULL){
            return $result;
        }
        //Loop through data structures(table or collection) in input array
        foreach ($input as $datastructure){
            //If data structure is sql perform sql delete
            if(getDbType($datastructure) =='sql'){
                //Trim sql_ prefix from name of a table
                $datastructure = str_replace('sql_', '',$datastructure);
                //If sql delete was successful add name of data structure to array of successful results
                if($this->db->query('DELETE FROM '.$datastructure) !== FALSE){
                    $result['clean_succ'][] = $datastructure;
                }else{
                    //If sql delete was NOT successful add name of data structure to array of FAILED results
                    $result['clean_fail'][] = $datastructure;
                }
            }else{
                //If data structure is not sql, then it is mongodb, so perform a MongoDB specific Deletion process
                //Assign collection to variable
                $collection = $this->mongoClient->extracthub->$datastructure;
                //Delete everything from this collection
                $collection->deleteMany([]);
                //Add name of this collection to the array of successful results
                $result['clean_succ'][] = $datastructure;
            }

        }
        return $result;
    }

    /**
     * Getter which allows to retrieve current application phase from database
     * @return array - phase
     */
    public function get_phase(){
        //Prepare sql query.
        $sql = 'select state from phase';
        //Run query
        $query = $this->db->query($sql);
        //If query failed return error
        if($query === FALSE){
            return $this->db->error();
            }
        //Assign query result to array
        $result = $query->result_array();
        //Assign values of `state` columns to $currentphase variable
        $currentphase = $result[0]['state'];
        return $currentphase;
    }

    /**
     * Setter which allows to change application phase in database
     * @param $phase
     * @return array|bool - true or error while connection to database
     */
    public function set_phase($phase){
        //Prepare sql query. Update table `phase` with new phase value
        $sql = 'update phase set state = \''.$phase.'\';';
        //Run query
        $query = $this->db->query($sql);
        //If query failed return error
        if($query === FALSE){
            return $this->db->error();
        }
        return true;
    }

    /**
     * Getter which allows to retrieve settings from database
     * @return array - array of key=>value settings
     */
    public function get_settings(){
        //Prepare sql query. Select settings from `settings` table
        $sql = 'select * from settings';
        //Run query
        $query = $this->db->query($sql);
        //If query failed return error
        if($query === FALSE){
            return $this->db->error();
        }
        //Assign query result to array
        $result = $query->result_array();
        //Assign values of settings columns to appropriate variables
        $category = $result[0]['category'];
        $restriction = $result[0]['restriction_level'];
        return ['category'=>''.$category,'restriction_level'=>$restriction];
    }

    /**
     * Setter which allows to write settings to database
     * @param array $settings
     * @return array|bool
     */
    public function set_settings(array $settings){
        //Prepare sql query. Update table `settings` with new settings values
        $sql = 'update settings set category = \''.$settings['category'].'\', restriction_level = \''.$settings['restriction_level'].'\';';
        //Run query
        $query = $this->db->query($sql);
        //If query failed return error
        if($query === FALSE){
            return $this->db->error();
        }
        return true;
    }

    /**
     * Function that checks if page is allowed to be opened at current phase
     * @param $page - page to check
     * @param $phase - phase
     * @return array|bool - allowed or not or error during database query
     */
    public function check_restrictions($page, $phase){
        //Get settings from database
        $settings = $this->get_settings();
        //Get current restriction level from settings array
        $restriction_level = $settings['restriction_level'];

        if ($restriction_level == 'strict'){
            //If restriction level is strict then allow only pages which are the same as current phase. e.g. page extract will be opened only on phase extract
            return $page == $phase;
        }elseif ($restriction_level == 'development'){
            //If restriction level is development then return true for any page and allow any page
            return true;
        }elseif ($restriction_level == 'soft'){
            //If restriction level is soft then allow to run page which corresponds to phase and previous one
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
                    //If database was corrupted and phase is not one of the values "extract", "transform" or "load" return error
                    return ['error'=>'phase is not valid, check database'];
            }

        }
        //If database was corrupted and restriction level is not one of the values "development", "soft" or "strict" return error
        return ['error'=>'restriction_level is not valid, check database'];
    }

    /**
     * Function that allows to download csv file with the content of query
     * @param $sql_query - sql query
     */
    public function get_csv($sql_query){
        //Load helpers
        $this->load->dbutil();
        $this->load->helper('file');
        $this->load->helper('download');
        //Query database
        $query = $this->db->query($sql_query);
        //Set delimiter to comma
        $delimiter = ",";
        //Set newline character
        $newline = "\r\n";
        //generate csv file from query result
        $data = $this->dbutil->csv_from_result($query, $delimiter, $newline);
        //force csv file download
        force_download('CSV_Report.csv', $data);
    }
}