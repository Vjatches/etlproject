<div class="windows">
    <div class="app" style="width:520px">
        <h1 id="welcome-to-cas-ci-documentation">Customise Transform options</h1>
        <hr>
        <?php echo validation_errors(); ?>
        <?= form_open('transform', array('class' => '.form-horizontal', 'id' => 'form_transform')) ?>
        <div class="container">
            <div class="row">
                <p>Choose set of data to proceed with</p>
            </div>
            <div class="row">
                <?php
                $itemcounter = 0;
                foreach ($checkboxes as $checkbox) {
                    echo '<div class="col-sm"><label class="checkbox-inline"><input type="checkbox" id="' . $checkbox['id'] . '" name="fields[]" value="' . $checkbox['value'] . '">' . $checkbox['label'] . '</label></div>';
                    $itemcounter++;
                    if ($itemcounter == 3) {
                        echo '</div><div class="row">';
                        $itemcounter = 0;
                    }
                }
                ?>
                <div class="col-6 col-centered"><label class="checkbox-inline"><input type="checkbox" id="checkall_chb" value="checkall">Check everything</label></div>
            </div>
        </div>
        <hr>
        <div class="container" style="margin-top:25px">
            <div class="row">
                <div class="col-8"><label><input type="checkbox" id="default_chb"  value="default">Save as default<br><span style="margin-left: 18px">transform profile</span></label></div>
                <div class="col-4"><span class="input-group-btn "><input id="submit" class="btn btn-info" type="submit" name="submit" value="Transform"/></span></div>
            </div>
        </div>
        </form>
    </div>