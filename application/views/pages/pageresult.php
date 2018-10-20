<div class="app">
    <h1>Loaded <?=count($content['product'])?> items in <?=$content['executiontime']?></h1>
    <ul>
        <ul><li><?=$content['product']?></li></ul>
        <?php /*foreach ($content['product'] as $item): */?><!--

            <li><ul><li><?/*=$item['title']*/?></li><li><?/*=$item['price']*/?></li><li><?/*=$item['seller']*/?></li></ul></li>

        --><?php /*endforeach; */?>
    </ul>
</div>
</div>
