<?= script_tag('js/modernizr-2.8.3.min.js') ?>
<?= script_tag('js/highlight.pack.js') ?>
<script>var base_url = '.';</script>

<?= script_tag('js/jquery-3.3.1.min.js') ?>
<?= script_tag('js/jquery.json-viewer.js') ?>
<?= script_tag('js/bootstrap.min.js') ?>
<?= script_tag('js/jquery-ui.min.js') ?>
<?= script_tag('js/jquery-confirm.min.js') ?>

<script type="text/javascript">
    $(document).ready(function () {

        var askConfirmation = true;

        $("#form_delete").submit(function(e) {
            if (askConfirmation) {
                e.preventDefault(); // dont submit the form, ask for confirmation first.
                $.confirm({
                    title: "Erase <b><?=isset($content['table_name']) ? $content['table_name'] : ""?></b>",
                    content: "Are you sure you want to delete all records from <span style=\"color: red\"><?=isset($content['table_name']) ? $content['table_name'] : ""?></span> ?",
                    buttons: {
                        confirm: {
                            text: "Delete",
                            btnClass: 'btn-danger',
                            action: function() {
                                askConfirmation = false; // done asking confirmation, now submit the form
                                $('#delete').click();
                            }
                        },
                        cancel: {
                            text: "Cancel",
                            btnClass: 'btn-default'
                        }
                    }
                });
            }
        });

        $("#query").keypress(function (e) {
            if (e.which == 13 && !e.shiftKey) {
                $("#submit").click();
                e.preventDefault();
                return false;
            }
        });


        $("#checkall_chb").change(function () {
            if (this.checked) {
                $("[name='fields[]']").each(function () {
                    this.checked = true;
                });
            } else {
                $("[name='fields[]']").each(function () {
                    this.checked = false;
                });
            }
        });

        <?php
        if ($toccurrent == 'emongocrud' || $toccurrent == 'tmongocrud') {
                foreach ($content['documents'] as $id => $document) {
                    echo '$(\'#' . $id . '\').jsonViewer(' . $document . ', {collapsed: true});';
                }
        }
        ?>


    });


</script>
</body>
</html>

}