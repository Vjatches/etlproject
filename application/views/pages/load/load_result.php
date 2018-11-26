<div class="app">
    <h1>Load report:</h1>
    <hr>
    <p>Tried to load <b><?= $rowsqty ?></b> items into Data Warehouse in <b><?= $content['executiontime'] ?> s</b></p>
    <span><b>Result:</b></span>
    <ul>
        <li>Amount of <span style="color:green">New</span> records inserted to Warehouse: <b><span style="color:green"><?=$content['inserted']?></span></b></li>
        <li>Amount of <span style="color:blue">Updated</span> records in Warehouse: <b><span style="color:blue"><?=$content['updated']?></span></b></li>
        <li>Amount of <span style="color:red">Not affected</span> records in Warehouse: <b><span style="color:red"><?=$content['not_affected']?></span></b></li>
    </ul>
    <div class="rst-footer-buttons" role="navigation" aria-label="footer navigation">

        <a href="<?=base_url()?>sql/products" class="btn float-right" title="Load">Proceed to CRUD <span class="icon icon-circle-arrow-right"></span></a>
        <a href="<?=base_url()?>load" class="btn btn-success" title="Load"><span class="icon icon-circle-arrow-left"></span> Back to Load</a>

    </div>
</div>
</div>