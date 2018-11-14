<div class="windows">
<div class='app'>

    <div class="error"><h1>MODULE ACCESS ERROR</h1>
        <h4>Reason: </h4>
        <p>
            Trying to use incorrect module
        </p>
        <h4>Message:</h4>
    <p>
        You are trying to access module incompatible with application phase.
    </p>
    </div>
    <span><b>Please visit compatible module:</b></span>
    <ul>
        <?php if ($phase == 'extract'):?>
        <li><a href="<?=base_url()?>extract">Extract - start ETL from here</a></li>
        <?php elseif ($phase == 'transform'):?>
            <li><a href="<?=base_url()?>transform">Transform - continue ETL workflow</a></li>
        <?php elseif ($phase == 'load'):?>
            <li><a href="<?=base_url()?>load">Load - finish ETL workflow</a></li>
       <?php endif;?>

    </ul>

</div>