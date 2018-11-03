<div class="windows">
    <div class="app">
        <h1 id="welcome-to-cas-ci-documentation">Extract links from allegro category</h1><hr>


        <?php echo validation_errors(); ?>

        <?=form_open('extractPage', array('class' => '.form-horizontal', 'id' => 'form_search'))?>

        <div class="form-group">
            <label class="control-label" for="pageUrl">Page link</label>
            <div class="col-sm-10">
                <input class="form-control" type="text" placeholder="Enter links to an item" name="pageUrl" id="pageUrl"/>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <span class="input-group-btn">
<input id="submit" class="btn btn-danger" type="submit" name="submit" value="Get Page" />
	</span>
            </div>
        </div>


        </form>
    </div>