<div class="app">
    <h1>Loaded <?= count($content['product']) ?> items in <?= $content['executiontime'] ?></h1>
    <ul>
        <!-- <ul><li><? /*=$content['product']*/ ?></li></ul>-->
        <?php foreach ($content['product'] as $item): ?>

            <li>
                <ul>
                    <?php foreach($item as $key=>$value): ?>
                    <li><?= $key ?> : <?= $value?></li>
                    <?php endforeach; ?>
                </ul>
            </li>

        <?php endforeach; ?>
    </ul>
</div>
</div>
