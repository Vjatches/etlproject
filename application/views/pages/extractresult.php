<div class="app">
    <h1>Extracted <?= $content['amount']['parsed'] ?> items from Allegro in <?= $content['executiontime'] ?></h1>
    <ul>
        <li>Amount of  New records inserted to temporary database: <b><?=$content['amount']['affected']?></b></li>
        <li>Amount of records already present in database: <b><?=$content['amount']['notaffected']?></b></li>
    </ul>
</div>
</div>
