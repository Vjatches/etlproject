<div class="windows">
    <div class="app">
        <h1 id="welcome-to-cas-ci-documentation">Extract products from Allegro category</h1>
        <hr>


        <?php echo validation_errors(); ?>

        <?= form_open('extract', array('class' => '.form-horizontal', 'id' => 'form_search')) ?>

        <div class="form-group">
            <div class="col-sm-5" style="margin-bottom: 10px;margin-top: 10px;padding-left: 8px;">
                <input class="form-control" type="number" placeholder="Enter value" name="amountOfPages"
                       id="amountOfPages"/>
            </div>
            <label class="control-label" for="amountOfPages">Enter amount of pages to process</label>
            <ul>
                <li>Minimum: <b>1</b></li>
                <li>Maximum: <b><?=$pagesqty?></b></li>
            </ul>
            <div class="row">

                <span class="input-group-btn float-right" style="margin-left: 300px"><input id="submit" class="btn btn-danger" type="submit" name="submit"
                                                      value="Extract"/></span>
            </div>



        </div>


        </form>
    </div>