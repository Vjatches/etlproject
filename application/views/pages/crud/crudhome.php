<h1>CRUD Home Page</h1>
<hr>
<p>Please choose database on which you want to perform CRUD operations from the left menu.</p>
<span>Description of menu options:</span>
<ul>
    <li><a href="<?=base_url()?>emongocrud">E: Mongo</a> - Mongo DB Collection `products` where raw data from html lands</li>
    <li><a href="<?=base_url()?>tmongocrud">T: Mongo</a> - Mongo DB Collection `aggregated` where aggregated selected data lands during transform</li>
    <li><a href="<?=base_url()?>tsqlcrud">T: SQL</a> - SQL Table with structured data with converted data-types after full transform</li>
    <li><a href="<?=base_url()?>lsqlcrud">L: SQL</a> - SQL Table which represents target Data Warehouse</li>
</ul>