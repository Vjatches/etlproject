<div class="windows">

<div class="app">
    <h1>Extract report:</h1>
    <hr>
    <p>Extracted <b><?= $content['extract']['amount']['parsed'] ?></b> items from Allegro in <b><?= $content['extract']['executiontime'] ?></b></p>
    <ul>
        <li>Amount of New records inserted to temporary database: <b><?=$content['extract']['amount']['affected']?></b></li>
        <li>Amount of records already present in database: <b><?=$content['extract']['amount']['notaffected']?></b></li>
    </ul>
</div>

<div class="app">
    <h1>Transform report:</h1>
    <hr>
    <p>Transform process has been executed in <b><?= $content['transform']['executiontime'] ?> s</b></p>
    <ul>
        <li>MongoDB aggregation status:
            <?php if (is_object($content['transform']['mongodb'])):?>
                <b><span style="color: green">OK</span></b>
            <?php else: ?>
                <b><span style="color: red">NOT OK</span></b>
            <?php endif; ?>
        </li>
        <?php if (count($content['transform']['mysql']['failed'])==0):?>
            <li>MySQL insert status: <b><span style="color: green">OK</span></b></li>
        <?php else: ?>
            <li>Following products had not been inserted:
                <?php foreach ($content['transform']['mysql']['failed'] as $id): ?>
                    <?= $id?>
                <?php endforeach; ?>
            </li>
        <?php endif; ?>
        <li>Amount of records inserted to temporary database: <b><?=$content['transform']['mysql']['numrows']?></b></li>
    </ul>

</div>

<div class="app">
    <h1>Load report:</h1>
    <hr>
    <p>Load process executed in <b><?= $content['load']['executiontime'] ?> s</b></p>
    <span><b>Result:</b></span>
    <ul>
        <li>Amount of <span style="color:green">New</span> records inserted to Warehouse: <b><span style="color:green"><?=$content['load']['inserted']?></span></b></li>
        <li>Amount of <span style="color:blue">Updated</span> records in Warehouse: <b><span style="color:blue"><?=$content['load']['updated']?></span></b></li>
        <li>Amount of <span style="color:red">Not affected</span> records in Warehouse: <b><span style="color:red"><?=$content['load']['not_affected']?></span></b></li>
    </ul>
</div>

    <?php if(isset($content['cleanup']['clean_succ'])) : ?>
    <div class="app">
        <h1>Cleanup report:</h1>
        <hr>
        <ul>
            <?php foreach ($content['cleanup']['clean_succ'] as $table_name): ?>
            <li>Cleaned up table: <b><?=$table_name?></b></li>
            <?php endforeach;?>
        </ul>
    </div>
    <?php endif;?>

</div>