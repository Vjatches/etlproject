<div class="app" style="display:inline-block">
    <div class="<?php  echo form_error('query') ? 'error' : ''; ?>">
        <?php echo validation_errors(); ?>
    </div>
    <?= form_open($toccurrent, array('class' => '.form-horizontal', 'id' => 'form_search')) ?>
    <div class="form-group">
        <div class="input-group" style="width: 500px;">
            <div class="input-group-prepend">
                <span class="input-group-btn input-group-text"><input id="submit" class="btn btn-danger" type="submit" name="submit" value="Run Query"/></span>
            </div>
            <textarea  rows="1" class="form-control" type="text" id="query" name="query" placeholder="SELECT ... FROM temp_products WHERE ..."></textarea>
        </div>
    </div>

    </form>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">Last query:</span>
        </div>
        <textarea  rows="1" class="form-control" type="text" id="last_query" name="last_query" readonly><?=isset($content['query']) ? $content['query'] : ''?></textarea>
    </div>
</div>

<div class="card mb-3">
    <?php if ($content['success']!='1'):?>
        <div class="error"><span>Database error:</span>
        <ul>
            <li>
                <b>Code:</b> <?=$content['error']['code']?>
            </li>
            <li>
                <b>Message:</b> <?=$content['error']['message']?>
            </li>
        </ul>
        </div>
    <?php else: ?>
    <div class="card-header">
        Table <b><?=$content['table_name']?></b> shown <b><?=count($content['rows'])?></b> rows out of <b><?=$content['numrows']?></b>
        <?= form_open($toccurrent, array('name' => 'form_delete', 'id' => 'form_delete')) ?>
        <span class="input-group-btn input-group-text float-right"><input id="delete" class="btn btn-danger" type="submit" name="delete" value="X"/></span>
        </form>
    </div>
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>#</th>
                    <?php foreach ($content['column_names'] as $column_name):?>
                    <th><?=$column_name?></th>
                    <?php endforeach;?>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>#</th>
                    <?php foreach ($content['column_names'] as $column_name):?>
                        <th><?=$column_name?></th>
                    <?php endforeach;?>
                </tr>
                </tfoot>
               <tbody>
               <?php $count = 0;?>
               <?php foreach ($content['rows'] as $row):?>
               <tr>
                   <?php $count+=1;?>
                   <td><?=$count?></td>
                   <?php foreach ($row as $column):?>
                        <td><div><?=$column?></div></td>
                   <?php endforeach;?>
               </tr>
               <?php endforeach;?>
               </tbody>
            </table>
        </div>

    </div>
    <?php endif;?>
</div>