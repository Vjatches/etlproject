<div class="windows">
    <div class="app" style="min-width: 700px">
        <h1>Application settings</h1>
        <hr>

        <div class="<?php  echo form_error('category') ? 'error' : ''; ?>">
            <?php echo validation_errors(); ?>
        </div>
        <?= form_open('settings', array('class' => '.form-horizontal', 'id' => 'form_search')) ?>
        <div class="form-group">

            <div class="container" style="margin-top:20px">
                <div class="row">
                    <label class="control-label col-sm-4" for="category"><b>Category link (URL):</b></label>
                    <div class="col-sm-8">
                        <input class="form-control" type="text" placeholder="https://allegro.pl/kategoria/XXXXXX" name="category"
                               id=category" value="<?=isset($settings['category']) ? $settings['category'] : "ERROR: CAN'T GET CATEGORY"?>" />
                    </div>
                </div>
                <div class="row" style="margin-top:20px;">
                    <label class="control-label col-sm-4" for="restriction_level"><b>Flow restriction level:</b></label>
                    <div class="col-sm-4">
                        <select id="restriction_level" name="restriction_level" class="form-control form-control-sm">
                            <option value="strict"  <?=isset($settings['restriction_level'])&&$settings['restriction_level']=='strict' ? "selected=\"selected\"" : ""?>>Strict</option>
                            <option value="soft" <?=isset($settings['restriction_level'])&&$settings['restriction_level']=='soft' ? "selected=\"selected\"" : ""?>>Soft</option>
                            <option value="development" <?=isset($settings['restriction_level'])&&$settings['restriction_level']=='development' ? "selected=\"selected\"" : ""?>>Development</option>
                        </select>
                    </div>
                </div>
                <div class="row">

                <span class="input-group-btn float-right" style="margin-left: 520px;margin-top: 50px;"><input id="submit" class="btn btn-danger form-control" type="submit" name="submit"
                                                                                            value="Save settings"/></span>
            </div>
            </div>

        </div>

        </form>
    </div>