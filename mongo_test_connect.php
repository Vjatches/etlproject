<?php
require 'vendor/autoload.php';

$title = "Macbook 13";
$price = "12345";
$seller = "vasya";

$items = [
    [
        "_id"=>"1",
        "title"=>$title,
        "price"=>$price,
        "seller"=>$seller
    ],
    [
        "_id"=>"5",
        "title"=>$title,
        "price"=>$price,
        "seller"=>$seller
    ],
    [
        "_id"=>"8",
        "title"=>$title,
        "price"=>$price,
        "seller"=>$seller
    ],
    [
        "_id"=>"9",
        "title"=>$title,
        "price"=>$price,
        "seller"=>$seller
    ],
];

$bulk = new \MongoDB\Driver\BulkWrite(['ordered'=>false]);
foreach ($items as $item){
    $bulk->update(
        ['_id' =>$item['_id']],
        array('$setOnInsert' => $item),
        array('upsert' => true)
    );
}

$manager = new \MongoDB\Driver\Manager('mongodb+srv://root:root@kreslav-hcr9i.mongodb.net');
$writeConcern = new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY, 100);

$result = $manager->executeBulkWrite('extracthub.products', $bulk, $writeConcern);

printf("Inserted %d document(s)\n", $result->getInsertedCount());
printf("Matched  %d document(s)\n", $result->getMatchedCount());
printf("Updated  %d document(s)\n", $result->getModifiedCount());
printf("Upserted %d document(s)\n", $result->getUpsertedCount());
printf("Deleted  %d document(s)\n", $result->getDeletedCount());

