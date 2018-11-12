<div class="app">
    <div class="<?php  echo form_error('query') ? 'error' : ''; ?>">
        <?php echo validation_errors(); ?>
    </div>
    <?= form_open('tsqlcrud', array('class' => '.form-horizontal', 'id' => 'form_search')) ?>
    <div class="form-group">
        <label for="query">SQL query (Please add LIMIT limitation):</label>
        <input class="form-control col-sm-5" type="text" id="query" name="query"/>
                <span class="input-group-btn"><input id="submit" class="btn btn-danger" type="submit" name="submit"
                                                                                            value="Run Query"/></span>
    </div>

    </form>
</div>

<div class="card mb-3">
    <?php if ($content['success']!='1'):?>
        <div class="error"><p>Please provide valid <b>SELECT</b> query string to table <b><?=$content['table_name']?></b></p></div>
    <?php else: ?>
    <div class="card-header">
        Table <b><?=$content['table_name']?></b> shown <b><?=count($content['rows'])?></b> rows out of <b><?=$content['numrows']?></b>
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