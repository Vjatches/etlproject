<div class="windows">

    <div class="container">
        <div class="row">
            <div class="<?php  echo form_error('amountOfPages') ? 'error' : ''; ?>">
                <?php echo validation_errors(); ?>
            </div>
            <?= form_open('home', array('class' => '', 'id' => 'form_etl')) ?>
        </div>
        <div class="row">
            <div class="app">
                <h1 id="extract-header"><span style="color:#8d0126">Extract</span> settings</h1>
                <hr>

                <div class="form-group" style="margin-bottom: 0">
                    <div class="col-sm-8" style="margin-bottom: 10px;margin-top: 10px;padding-left: 8px;">
                        <input class="form-control" type="number" placeholder="Enter pages" name="amountOfPages"
                               id="amountOfPages"/>
                    </div>
                    <label class="control-label" for="amountOfPages">Enter amount of pages to process</label>
                    <ul style="margin-bottom: 0">
                        <li>Minimum: <b>1</b></li>
                        <li>Maximum: <b><?=$pagesqty?></b></li>
                    </ul>

                </div>


            </div>
            <div class="app">
                <h1 id="welcome-to-cas-ci-documentation" ><span style="color:#004f48" >Load</span> settings</h1>
                <hr>
                <div class="form-group" style="margin-bottom: 0">
                    <div class="col-sm-7" style="margin-bottom: 10px;margin-top: 10px;padding-left: 8px;">
                        <input class="form-control" type="number" placeholder="Enter limit" name="numrows"
                               id="numrows"/>
                    </div>
                    <label class="control-label" for="numrows">Enter amount of rows to limit Data Warehouse load query</label>
                    <p>Provide <b>0</b> if you do not want any limit</p>


                </div>


            </div>
        </div>
        <div class="row">
            <div class="app" style="min-width: 500px">
                <h1><span style="color:#27018d">Transform</span> settings</h1>
                <hr>
                <div class="container">
                    <div class="row">
                        <p>Choose set of data to proceed with</p>
                    </div>
                    <div class="row">
                        <?php
                        $itemcounter = 0;
                        foreach ($checkboxes as $checkbox) {
                            $checked = 'checked';
                            if(array_search($checkbox['value'], $choice) === FALSE){
                                $checked = '';
                            }
                            echo '<div class="col-sm"><label class="checkbox-inline"><input type="checkbox" '.$checked.' id="' . $checkbox['id'] . '" name="fields[]" value="' . $checkbox['value'] . '">' . $checkbox['label'] . '</label></div>';
                            $itemcounter++;
                            if ($itemcounter == 3) {
                                echo '</div><div class="row">';
                                $itemcounter = 0;
                            }
                        }
                        ?>
                        <hr>
                        <div class="col-6 col-centered" style="margin-top:20px"><label class="checkbox-inline"><input type="checkbox" id="checkall_chb" value="checkall">Check everything</label></div>
                    </div>
                </div>

                <div class="container" style="margin-top:25px">
                </div>

            </div>
            <div class="app">

                <div class="container">
                    <h1>Customise data clean up</h1>
                    <hr>
                    <div class="row">
                        <p>Choose which temporal data structures to clean up</p>
                    </div>

                    <div class="row">
                        <div class="col-12"><label><input type="checkbox" id="products_chb" name="cleanups[]" value="products"><b>products</b> [Extract : Mongo collection]</span></label></div>
                    </div>
                    <div class="row">
                        <div class="col-12"><label><input type="checkbox" id="aggregated_chb" name="cleanups[]" value="aggregated"><b>aggregated</b> [Transform : Mongo collection]</span></label></div>
                    </div>
                    <div class="row">
                        <div class="col-12"><label><input type="checkbox" id="tempproducts_chb" name="cleanups[]" value="temp_products"><b>temp_products</b> [Transform : MySQL table]</span></label></div>
                    </div>
                    <hr>
                    <div class="row" style="margin-top:20px">
                        <div class="col-6 col-centered"> <span class="input-group-btn"><input id="submit" class="btn btn-danger btn-big" type="submit" name="submit" value="S T A R T"/></span></div>
                    </div>
                </div>

                </form>
            </div>
        </div>
    </div>

