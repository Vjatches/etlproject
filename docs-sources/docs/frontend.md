# Frontend Logic

Application frontend logic is written partially in JavaScript and in Php.

Javascript plays supportive role and is mainly used for data presentation.

# JavaScript

Function that processes user confirmation for deleting everything from given database.

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
            
Function that allows to submit sql query or mongo filter on `Enter` keypress.

    $("#query").keypress(function (e) {
                if (e.which == 13 && !e.shiftKey) {
                    $("#submit").click();
                    e.preventDefault();
                    return false;
                }
            });
            
Function that handles 'Check all' checkbox behavior.

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
            
Function which is used to generate MongoDB crud interface.

    <?php
            if ($toccurrent == 'mongo/products' || $toccurrent == 'mongo/aggregated') {
                    foreach ($content['documents'] as $id => $document) {
                        echo '$(\'#' . $id . '\').jsonViewer(' . $document . ', {collapsed: true});';
                    }
            }
    ?>

## Libraries

Following libraries are used to support frontend of the application.

    <?= script_tag('js/modernizr-2.8.3.min.js') ?>
    <?= script_tag('js/highlight.pack.js') ?>    
    <?= script_tag('js/jquery-3.3.1.min.js') ?>
    <?= script_tag('js/jquery.json-viewer.js') ?>
    <?= script_tag('js/bootstrap.min.js') ?>
    <?= script_tag('js/jquery-ui.min.js') ?>
    <?= script_tag('js/jquery-confirm.min.js') ?>

