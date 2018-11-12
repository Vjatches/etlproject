<div class="windows">
    <div class="app">
        <h1 id="welcome-to-cas-ci-documentation">Load products to Data Warehouse</h1>
        <hr>


        <div class="<?php  echo form_error('numrows') ? 'error' : ''; ?>">
            <?php echo validation_errors(); ?>
        </div>
        <?= form_open('load', array('class' => '.form-horizontal', 'id' => 'form_search')) ?>


        <div class="form-group">
            <div class="col-sm-7" style="margin-bottom: 10px;margin-top: 10px;padding-left: 8px;">
                <input class="form-control" type="number" placeholder="Enter number of rows" name="numrows" value="<?=$rowsqty?>"
                       id="numrows"/>
            </div>
            <label class="control-label" for="numrows">Enter amount of rows to load</label>
            <ul>
                <li>Minimum: <b>1</b></li>
                <li>Maximum: <b><?=$rowsqty?></b></li>
            </ul>
            <div class="row">

                <span class="input-group-btn float-right" style="margin-left: 270px"><input id="submit" class="btn btn-success" type="submit" name="submit"
                                                                                            value="Load"/></span>
            </div>

        </div>

        </form>
    </div>