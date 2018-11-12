<div class="app">
    <h1>Transform report:</h1>
    <hr>
    <p>Transform process has been executed in <b><?= $content['executiontime'] ?></b></p>
    <ul>
        <li>MongoDB aggregation status:
        <?php if (is_object($content['mongodb'])):?>
            <b><span style="color: green">OK</span></b>
        <?php else: ?>
            <b><span style="color: red">NOT OK</span></b>
        <?php endif; ?>
        </li>
       <?php if (count($content['mysql']['failed'])==0):?>
            <li>MySQL insert status: <b><span style="color: green">OK</span></b></li>
        <?php else: ?>
            <li>Following products had not been inserted:
                <?php foreach ($content['mysql']['failed'] as $id): ?>
                    <?= $id?>
                <?php endforeach; ?>
            </li>
        <?php endif; ?>
        <li>Amount of records inserted to temporary database: <b><?=$content['mysql']['numrows']?></b></li>
    </ul>

    <div class="rst-footer-buttons" role="navigation" aria-label="footer navigation">

        <a href="<?=base_url()?>load" class="btn btn-success float-right" title="Load">Proceed to Load <span class="icon icon-circle-arrow-right"></span></a>

        <a href="<?=base_url()?>transform" class="btn btn-info" title="Transform"><span class="icon icon-circle-arrow-left"></span> Back to Transform</a>

    </div>
</div>
</div>
