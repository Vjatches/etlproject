# Application Models

Application makes use of 4 models, each one handles business logic of respective module of the program.

![alt text](img/models.png)

* `Extract_model` - handles the Extract process as well as manages the connection with MongoDB temporal collections.

* `Transform_model` - handles the Transform process as well as manages the connection with MongoDB and MySQL temporal databases.

* `Load_model` - handles the Load process as well as manages the connection with target Data Warehouse.

* `Crud_model` - handles all logic which is responsible for interaction with both MongoDB and MySQL databases. Is used in all other models and a controller.


# Extract Model

![alt text](img/extractmodel.png)

This model handles all the logic behind Extract process. 
It connects to remote web-page, scraps data and inserts it into temporal MongoDB collection named `extracted`.

## Fields

    /**
     * Variable which contains global application settings
     * @var
     */
    private $settings;

## Constructor

    /**
     * Extract_model constructor.
     * Loads helpers, other models, libraries and settings
     */
    function __construct()
    {
        //Load crud model so we can read settings from database
        $this->load->model('crud_model');
        //Load settings from database via crud_model
        $this->settings = $this->crud_model->get_settings();

        //Load helpers with usefull functions
        $this->load->helper('url'); //built-in
        $this->load->helper('extract'); //custom
    }

## Main Function

    /**
     * Main function of Extract process which uses all other functions
     * @param int $amountOfPages - amount of pages to extract from allegro category
     * @return mixed - returns report with data about extract process
     */
    public function runExtractorAsync($amountOfPages = 1)
    {
        //Load timer library
        $this->load->library('timer');
        //Start timer
        $this->timer->start();
        //Set php timeout to 0 so it will not timeout during lengthy scraping process
        set_time_limit(0);
        //Extract links from allegro category pages
        $links = $this->extractLinks($amountOfPages);
        //Create multi-threading loop
        $loop = React\EventLoop\Factory::create();
        //Create virtual browser which will process pages
        $client = new Browser($loop);
        //Load scraper class which will scrape data from products
        $this->load->library('scraper');
        //Set browser client for scraper
        $this->scraper->setClient($client);
        //Set data on which scraper will operate. Links is an array of product links from allegro category, 10 is an amount of simultaneous request
        $this->scraper->scrape($links, 10);
        //Start multi-threading loop
        $loop->run();
        //After loop finished looping, retrieve data from scraper
        $products = $this->scraper->getData();
        //Insert data to database
        $query = $this->loadToDatabase($products);
        //Pack data to be sent as a report of a process
        $result = [
            'amount' => [
                'parsed' => count($links),
                'affected' => $query['inserted'],
                'notaffected' => $query['matched'],
                ],
            'executiontime' =>  $this->timer->stop()
        ];

        return $result;
    }

## Other Functions

Function which returns amount of pages in category.

    /**
     * Function which is used to get pages quantity from category
     * @return mixed - amount of pages
     */
    public function getPagesQuantity()
    {
        //Get category from settings
        $category[] = $this->settings['category'];
        //Load category crawler
        $this->load->library('categorycrawler', $category);
        //Send crawler to get amount of pages from current working category
        $amountOfPages = $this->categorycrawler->getAmountOfPages();
        return $amountOfPages;
    }

Function which extracts links.

    /**
     * Function which extracts links to products from current category
     * @param $amountOfPages - from how many pages we want to extract links
     * @return array - array with product links
     */
    public function extractLinks($amountOfPages)
    {
        //Set from how many pages from category we wont to extract links
        $count = $amountOfPages;
        $i = 1;
        //Empty container which will be populated with links
        $links = array();
        //Extract links from category pages in a loop while i is smaller that requested amount of pages
        while ($i <= $count) {
            //Create link to new category page which looks like https://allegro.pl/category?p=i where ?p=i part shows page from which we will extract links
            $category_page[0] = $this->settings['category'].'?p='.$i;
            //Create category crawler and send it to collect links from this page
            $crawlers = new categorycrawler($category_page);
            //Populate $links array with links which category crawler brought to us
            $links[] = $crawlers->getProductLinksFromPage();
            //Increase value of i
            $i++;
        }
        //Aggregate links to one-dimesional array for further ease of processing
        $links = toSingleArray($links);
        return $links;

    }
    
Function which loads data objects to database.
    
    /**
     * Function which loads extracted data to mongo database
     * @param array $array - array of extracted data
     * @return array - report of load process
     */
    public function loadToDatabase(array $array)
    {
        //Create a bulk object
        $bulk = new \MongoDB\Driver\BulkWrite(['ordered' => false]);
        //Populate bulk object with records to be inserted in database
        foreach ($array as $item) {
            $bulk->update(
                ['_id' => $item['notifyAndWatch']['offerId']],
                array('$setOnInsert' => $item),
                array('upsert' => true)
            );
        }
        //Create manager which will perform bulk write to a certain mongodb database. Provide credentials to database as parameter
        $manager = new \MongoDB\Driver\Manager('mongodb+srv://root:root@kreslav-hcr9i.mongodb.net');
        //Create error handler, if something stucks it will throw an appropriate error
        $writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 100);
        //Let manager execute bulk insert
        $insert_result = $manager->executeBulkWrite('extracthub.products', $bulk, $writeConcern);
        //Pack data and report how many records has been inserted and how many were already present in database
        return [
            'inserted' => $insert_result->getUpsertedCount(),
            'matched' => $insert_result->getMatchedCount()
        ];
    }

# Transform Model

![alt text](img/transformmodel.png)

This model handles all the logic behind Transform process. 
It collects data from `extracted` temporal collection, aggregates and pushes it to another temporal collection
`aggregated` and then converts data types and migrates data to `temp_products` MySQL table.

## Fields

    /**
     * Variable which holds object responsible for connection with MongoDB
     * @var \MongoDB\Client
     */
    private $mongoClient;

## Constructor

    /**
     * Transform_model constructor.
     * Here we connect to databases and load helpers
     */
    function __construct()
    {
        //connect to MongoDB
        $this->mongoClient= new \MongoDB\Client('mongodb+srv://root:root@kreslav-hcr9i.mongodb.net/test?retryWrites=true');
        //connect to mysql, connection parameters are in application/config/database.php
        $this->load->database('mysql');
        //Load helpers
        $this->load->helper('url');
        $this->load->helper('transform');
    }

## Main Function

    /**
     * Main function of Transform process which uses other functions
     * @param $input - set of checkboxes which show what data do we want to transform
     * @return mixed - report with data about transform process
     */
    public function runTransform($input){
        //Set php time limit to 0 so it wont timeout during long process of quering database
        set_time_limit(0);
        //Load timer library
        $this->load->library('timer');
        //Start time
        $this->timer->start();
        //Aggregate data from mongoDB and write aggregation report to $result['mongodb']
        $result['mongodb'] = $this->aggregateData($input);
        //Transform data from aggregated mongodb collection to temp_products sql table and write report to variable $result['mysql']
        $result['mysql'] = $this->transformToSql();
        //stop timer and write execution time to $result['executiontime'] variable
        $result['executiontime']=$this->timer->stop();
        return $result;
    }

## Other Functions

Setter which is used to save transform settings to database.

    /**
     * Function which is used to save information which checkboxes from transform page has to remain checked when we reload page
     * @param $input - array of checkboxes
     * @param $consent - do we want to set array of checkboxes as default or no
     */
    public function setChoice($input, $consent){
        //check if we really want to sent it as default
        if($consent != 'default'){
            //if not do nothing and exit function
            return;
        }
        //clear previous default set of checkboxes which are stored in database
        $this->db->query('DELETE from choice');
        //Start sql transaction. This query is used before loops to speed up sql inserts
        $transactionsql = 'START TRANSACTION';
        $this->db->query($transactionsql);
        //Insert information with names of checkboxes which we want to remain checked at later loads
        foreach ($input as $checkbox){
            $sql = 'insert into choice (checkbox) value ("'.$checkbox.'");';
            $this->db->query($sql);
        }
        //Commit transaction. This query is used after loop inserts to summarise transaction and speed it up
        $commitsql = 'commit';
        $this->db->query($commitsql);
    }

Getter which is used to retrieve transform settings.

    /**
     * Function which is used to retrieve information from database about which checkboxes should be checked on page load.
     * It loads default set of checkboxes
     * @return array - array of checkboxes which have to be checked
     */
    public function getChoice(){
        //Select everything from choice table
        $sql = 'select * from choice';
        $query = $this->db->query($sql);
        $result = [];
        //Write names of checkboxes to result array and return int
        foreach ($query->result() as $row){
            $result[] = $row->checkbox;
        }
        return $result;
    }
    
Function which aggregates data via Mongo aggregation pipeline.
    
    /**
     * Function which extracts only useful data from big mongoDB object and aggregates it to simple key=>value pair
     * @param array $arrayOfValues - set of checkboxes which show what data do we want to aggregate
     * @return Traversable - object which contains report about aggregation process
     */
    public function aggregateData(array $arrayOfValues){
        //Assign mongodb collection 'products' to a variable
        $extracted = $this->mongoClient->extracthub->products;
        //Generate aggregation options based on array of chosen values
        $options = $this->generateAggregateOptions($arrayOfValues);
        //Assign mongodb collection 'aggregated' to a variable
        $aggregated = $this->mongoClient->extracthub->aggregated;
        //Clear 'aggregated' collection from any previous data
        $aggregated->deleteMany([]);
        //Use aggregation pipeline framework with aggregation options on collection with big objects
        $result = $extracted->aggregate($options);
        return $result;
    }
    
Function which generates options for aggregation pipeline.

    /**
     * Function that sets and generates aggregation options
     * @param array $arrayOfValues - set of checkboxes which show what data do we want to aggregate
     * @return array - array of options to pass to aggregation pipeline
     */
    public function generateAggregateOptions(array $arrayOfValues){
        //Set what attributes do we want to project during aggregation
        //project id always
        $project = ["id"=>1];
        //Foreach attribute project it as its final name and reduce nesting to single level
        foreach ($arrayOfValues as $attribute){
            //Determine how deeply is attribute nested by exploding it by dot
            $explodedAttribute = explode('.', $attribute);
            //Amount of product elements = depth of nesting
            $amount = count($explodedAttribute);
            //If attribute is nested reduce nesting (e.g. price.installments.installmentsQuantity becomes just installmentsQuantity)
            if($amount>2){
                $project[$explodedAttribute[$amount-2].''.$explodedAttribute[$amount-1]] = '$'.$attribute;
            }else{
                $project[$explodedAttribute[1]] = '$'.$attribute;
            }

        }
        //Create aggregation pipeline
        $ops = array(
            array(
                '$project' => $project //array of options which configure how to project attributes
            ),
            array(
                '$out' => "aggregated" //output of aggregation write directly to `aggregated` collection
            )
        );
        //Return aggregation pipeline
        return $ops;
    }
    
MongoDB to MySQL migrator.

    /**
     * Function that migrates data from mongoDB to sql table
     * @return mixed - report on migration process
     */
    public function transformToSql(){
        //Refresh connection to sql database
        $this->db->reconnect();
        //Assign mongoDB collection to variable
        $mongodb = $this->mongoClient->extracthub->aggregated;
        //Find everything in this collection and assign to cursor object
        $cursor = $mongodb->find();
        //Container which will host failed inserts
        $failed =[];
        //Start sql transaction. This query is used before loops to speed up sql inserts
        $transactionsql = 'START TRANSACTION';
        $this->db->query($transactionsql);

        //Loop through all products in MongoDB cursor
        foreach ($cursor as $product){
            //If cursor doesn't have this value assign string "NULL" to it
            //During assignment convert boolean values to respective strings using helper methods
            $id= isset($product['_id']) ? $product['_id'] : 'NULL';
            $title= isset($product['title']) ? $product['title'] : 'NULL';
            $priceInteger= isset($product['priceInteger']) ? $product['priceInteger'] : 'NULL';
            $sellerName= isset($product['sellerName']) ? $product['sellerName'] : 'NULL';
            $sellerListingUrl= isset($product['sellerListingUrl']) ? $product['sellerListingUrl'] : 'NULL';
            $quantityWithLabel= isset($product['quantityWithLabel']) ? $product['quantityWithLabel'] : 'NULL';
            $quantity= isset($product['quantity']) ? $product['quantity'] : 'NULL';
            $description= isset($product['description']) ? $product['description'] : 'NULL';
            $superSellerActive= isset($product['superSellerActive']) ? $product['superSellerActive'] : 'NULL';
            $itemCondition= isset($product['itemCondition']) ? $product['itemCondition'] : 'NULL';
            $endingDate= isset($product['endingDate']) ? $product['endingDate'] : 'NULL';
            $endingDate = getAttributeOrNull($endingDate);
            $nextPrice= isset($product['nextPrice']) ? $product['nextPrice'] : 'NULL';
            $label= isset($product['label']) ? $product['label'] : 'NULL';
            $installmentsquantity= isset($product['installmentsquantity']) ? $product['installmentsquantity'] : 'NULL';
            $installmentsfree= isset($product['installmentsfree']) ? $product['installmentsfree'] : 'NULL';
            $installmentsprice= isset($product['installmentsprice']) ? $product['installmentsprice'] : 'NULL';


            //Perform INSERT .... ON DUPLICATE KEY UPDATE .... - insert value, if value with such primary key exists, update certain fields
            $sql = 'insert into temp_products VALUES (
                      '.getAttributeOrNull($id).',
                      '.getAttributeOrNull($title).',
                      '.trim(str_replace(' ','',getAttributeOrNull($priceInteger)), '\'').',
                      '.getAttributeOrNull($sellerName).',
                      '.getAttributeOrNull($sellerListingUrl).',
                     '.getAttributeOrNull($quantityWithLabel).',
                      '.getAttributeOrNull($quantity).',
                      '.getAttributeOrNull($description).',
                      '.convertBoolean($superSellerActive).',
                      '.str_replace('Condition', '',str_replace('http://schema.org/','',getAttributeOrNull($itemCondition))).',
                      '.$endingDate.',
                      '.getAttributeOrNull($nextPrice).',
                      '.getAttributeOrNull($label).',
                      '.getAttributeOrNull($installmentsquantity).',
                      '.convertBoolean($installmentsfree).',
                      '.getAttributeOrNull($installmentsprice).') 
                      ON DUPLICATE KEY UPDATE title = values(title),priceInteger = values(priceInteger),sellerName = values(sellerName),
                      sellerListingUrl = values(sellerListingUrl),quantityWithLabel = values(quantityWithLabel),quantity = values(quantity),description = values(description),superSellerActive = values(superSellerActive),
                      itemCondition = values(itemCondition),endingDate = values(endingDate),nextPrice = values(nextPrice),label = values(label),installmentsquantity = values(installmentsquantity),
                      installmentsprice = values(installmentsprice),installmentsfree = values(installmentsfree);';

            //If something went wrong add id of failed insert to $failed array
            if(!$this->db->query($sql)){
                $failed[] = $product['_id'];
            }
        }
        //Commit transaction. This query is used after loop inserts to summarise transaction and speed it up
        $commitsql = 'commit';
        $this->db->query($commitsql);
        //Pack data with errors and amount of rows affected to array and return it
        $result['failed']=$failed;
        $result['numrows']=$this->db->count_all('temp_products');
        return $result;
    }

# Load Model

![alt text](img/loadmodel.png)

This model handles all the logic behind Load process. 
It collects data from `temp_products` MySQL table, converts data types, appends timestamp and compares record with target data warehouse.
If the record with the same key exists, modify it and change `modify_id` and `modify_date` columns, otherwise insert.

## Constructor

    /**
     * Load_model constructor.
     * Here we connect to databases and load helpers
     */
    function __construct()
    {
        //connect to mysql, connection parameters are in application/config/database.php
        $this->load->database('mysql');
    }

## Main Function

    /**
     * Main function of Load process which uses other functions
     * @param $numrows - amount of rows to load from temp_products to products tables
     * @param $mod_id - identifier of a process which initiates load
     * @return array - report on load process
     */
    public function runLoad($numrows, $mod_id){
        //Load timer
        $this->load->library('timer');
        //start timer
        $this->timer->start();
        //Set imitial limit to unlimited
        $limit = '';
        //If user specified limit overwrite initial limit
        if($numrows!=0){
            $limit = 'limit '.$numrows;
        }
        //Prepare select query
        $selectsql = 'select * from temp_products '.$limit.';';
        //Select everything from temp_products
        $selectquery = $this->db->query($selectsql);
        //Start sql transaction. This query is used before loops to speed up sql inserts
        $transactionsql = 'START TRANSACTION';
        $this->db->query($transactionsql);
        //Create counters for inserted, updated and not affected rows
        $count_insert =0;
        $count_update = 0;
        $count_notaffected = 0;

        //Loop through all records which we selected from `temp_products` and compare with records in 'products'
        foreach ($selectquery->result() as $row){
            //Set id of a process which is used to create records
            $create_id = $mod_id;
            //Prepare sql: Insert if there is no such record, update on duplicate key if there is such record with different data
            $loadsql = 'insert into products (`_id`, title, price, seller_name, seller_url, coins, available_quantity, description, super_status, item_condition, auction_ending_date, next_price, popularity_data, installments_quantity, free_installments, installments_price, create_id) VALUES (
                      \''.$row->_id.'\',
                      \''.$row->title.'\',
                      \''.$row->priceInteger.'\',
                      \''.$row->sellerName.'\',
                      \''.$row->sellerListingUrl.'\',
                     \''.$row->quantityWithLabel.'\',
                      \''.$row->quantity.'\',
                      \''.$row->description.'\',
                      \''.$row->superSellerActive.'\',
                      \''.$row->itemCondition.'\',
                      \''.$row->endingDate.'\',
                      \''.$row->nextPrice.'\',
                      \''.$row->label.'\',
                      \''.$row->installmentsquantity.'\',
                      \''.$row->installmentsfree.'\',
                      \''.$row->installmentsprice.'\',
                      \''.$create_id.'\') 
                      ON DUPLICATE KEY UPDATE modify_id = values(create_id), title = values(title),price = values(price),seller_name = values(seller_name),
                      seller_url = values(seller_url),coins = values(coins),available_quantity = values(available_quantity),description = values(description),super_status = values(super_status),
                      item_condition = values(item_condition),auction_ending_date = values(auction_ending_date),next_price = values(next_price),popularity_data = values(popularity_data),installments_quantity = values(installments_quantity),
                      installments_price = values(installments_price),free_installments = values(free_installments);';
            //Run previously prepared sql
            $this->db->query($loadsql);
            //Increase counters based on what happened with record
            if($this->db->affected_rows()==1){
                $count_insert += 1;
            }elseif($this->db->affected_rows()==2){
                $count_update += 1;
            }else{
                $count_notaffected += 1;
            }
        }
        //Commit transaction. This query is used after loop inserts to summarise transaction and speed it up
        $commitsql = 'commit';
        $this->db->query($commitsql);

        //Pack and return report data: counters and execution time
        return ['executiontime'=>$this->timer->stop(),'inserted' => $count_insert, 'updated' => $count_update, 'not_affected'=>$count_notaffected];
    }

## Other Functions

    /**
     * Function which is used to get rows quantity from temp_products table
     * @return int - amount of rows
     */
    public function getRowsQuantity(){
        return $this->db->count_all('temp_products');
    }

# Crud Model

![alt text](img/crudmodel.png)

This model handles all the logic which is used in other models and controller when they need to perform Create Read Update Delete (CRUD) operations on
any data structures involved in application workflow, be it MongoDB collections or MySQL tables.

## Fields

    /**
     * variable which holds object responsible for connection with MongoDB
     * @var \MongoDB\Client
     */
    private $mongoClient;

## Constructor

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

## Other Functions

Function which allows us to perform filter on mongoDB collection.

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

Function which allows us to perform query on sql table.

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

Function which allows delete everything from table or collection.

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

Getter which allows to retrieve current application phase from database.

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

Setter which allows to change application phase in database.

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

Getter which allows to retrieve settings from database.

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

Setter which allows to write settings to database.

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

Function that checks if page is allowed to be opened at current phase.

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

Function that allows to download csv file with the content of query.

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
