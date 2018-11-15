<div class="windows">
<div class="app" style="display:inline-block">
    <div class="<?php  echo form_error('filter') ? 'error' : ''; ?>">
        <?php echo validation_errors(); ?>
    </div>
    <?= form_open($toccurrent, array('class' => '.form-horizontal', 'id' => 'form_search')) ?>
    <div class="form-group">
        <div class="input-group" style="width: 500px;">
            <div class="input-group-prepend">
                <span class="input-group-btn input-group-text"><input id="submit" class="btn btn-info" type="submit" name="submit" value="Apply Filter"/></span>
            </div>
            <textarea  rows="1" class="form-control" type="text" id="filter" name="filter" placeholder='{"filter":"example"}'></textarea>
        </div>
    </div>

    </form>
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">Last filter:</span>
        </div>
        <textarea  rows="1" class="form-control" type="text" id="last_filter" name="last_flter" readonly><?=isset($content['filter']) ? $content['filter'] : ''?></textarea>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        Collection <b><?=isset($content['table_name']) ? $content['table_name'] : ""?></b> shown <b><?=isset($content['documents']) ? count($content['documents']) : ""?></b> documents out of <b><?=isset($content['num_documents']) ? $content['num_documents'] : ""?></b>
        <?= form_open($toccurrent, array('name' => 'form_delete', 'id' => 'form_delete')) ?>
        <span class="input-group-btn input-group-text float-right"><input id="delete" class="btn btn-danger" type="submit" name="delete" value="X"/></span>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="max-height: 750px; max-width:1000px">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Document</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>#</th>
                    <th>Document</th>
                </tr>
                </tfoot>
                <tbody>
                <?php $count = 0;?>
                <?php if(isset($content)): ?>
                <?php foreach ($content['documents'] as $id=>$document):?>
                    <tr>
                        <?php $count+=1;?>
                        <td><?=$count?></td>
                        <td style="text-align: left">
                            <pre id="<?=$id?>">
                            </pre>
                        </td>
                    </tr>
                <?php endforeach;?>
                <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
</div>