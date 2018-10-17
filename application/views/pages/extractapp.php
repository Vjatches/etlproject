<div class="windows">
    <div class="app">
        <h1 id="welcome-to-cas-ci-documentation">Extract links from allegro category</h1><hr>


        <?php echo validation_errors(); ?>

        <?=form_open('extract', array('class' => '.form-horizontal', 'id' => 'form_search'))?>
        <div class="form-group">
            <label class="control-label" for="concurrent">Limit of concurrent requests</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="concurrent" value="10" placeholder="Enter amount of concurrent requests" name="concurrent">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="amountOfPages">Amount of pages</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" placeholder="Enter amount of pages to process" name="amountOfPages" id="amountOfPages"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <span class="input-group-btn">
<input id="submit" class="btn btn-danger" type="submit" name="submit" value="Extract" />
	</span>
            </div>
        </div>


        </form>
    </div>