<div class="app">
    <h1>Extract report:</h1>
    <hr>
    <p>Extracted <b><?= $content['amount']['parsed'] ?></b> items from Allegro in <b><?= $content['executiontime'] ?></b></p>
    <ul>
        <li>Amount of New records inserted to temporary database: <b><?=$content['amount']['affected']?></b></li>
        <li>Amount of records already present in database: <b><?=$content['amount']['notaffected']?></b></li>
    </ul>
    <div class="rst-footer-buttons" role="navigation" aria-label="footer navigation">

        <a href="<?=base_url()?>transform" class="btn btn-info float-right" title="Transform">Proceed to Transform <span class="icon icon-circle-arrow-right"></span></a>


        <a href="<?=base_url()?>extract" class="btn btn-danger" title="Extract"><span class="icon icon-circle-arrow-left"></span> Back to Extract</a>

    </div>
</div>
</div>
