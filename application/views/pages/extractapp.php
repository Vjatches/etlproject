<div class="windows">
    <div class="app">
        <h1 id="welcome-to-cas-ci-documentation">Extract links from allegro category</h1><hr>
        <p>Enter amount of pages to process</p>

        <?php echo validation_errors(); ?>

        <?=form_open('extract', array('class' => '', 'id' => 'form_search'))?>

        <div class="input-group">
            <input class="form-control" type="text" name="amountOfPages" id="query"/>

            <span class="input-group-btn">
<input id="submit" class="btn btn-danger" type="submit" name="submit" value="Extract" />
	</span>
        </div>
        </form>
    </div>