<h1>CRUD Home Page</h1>
<hr>
<p>Please choose database on which you want to perform CRUD operations from the left menu.</p>
<span>Description of menu options:</span>
<ul>
    <li><a href="<?=base_url()?>mongo/products">E: Mongo</a> - Mongo DB Collection `products` where raw data from html lands</li>
    <li><a href="<?=base_url()?>mongo/aggregated">T: Mongo</a> - Mongo DB Collection `aggregated` where aggregated selected data lands during transform</li>
    <li><a href="<?=base_url()?>sql/temp_products">T: SQL</a> - SQL Table with structured data with converted data-types after full transform</li>
    <li><a href="<?=base_url()?>sql/products">L: SQL</a> - SQL Table which represents target Data Warehouse</li>
</ul>