<div class="app">
    <h1>Zaladowano w <?=$content['executiontime']?></h1>
    <ul>

        <?php foreach ($content['product'] as $item): ?>

            <li><ul><li><?=$item['title']?></li><li><?=$item['price']?></li><li><?=$item['seller']?></li></ul></li>

        <?php endforeach; ?>

    </ul>
</div>
</div>
