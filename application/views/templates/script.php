<?=script_tag('js/modernizr-2.8.3.min.js')?>
<?=script_tag('js/highlight.pack.js')?>
<script>var base_url = '.';</script>

<?=script_tag('js/jquery-3.3.1.min.js')?>
<?=script_tag('js/bootstrap.min.js')?>
<?=script_tag('js/jquery-ui.min.js')?>

<script type="text/javascript">
	$(document).ready(function(){
		$("#query").autocomplete({
			source: "<?php echo site_url('get_autocomplete');?>",
			select: function(event, ui) {
				$("#query").val(ui.item.dn);
				console.log(ui.item);
				document.getElementById('submit').click();
				}
		});

        $("#checkall_chb").change(function() {
            if (this.checked) {
                $("[name='fields[]']").each(function() {
                    this.checked=true;
                });
            } else {
                $("[name='fields[]']").each(function() {
                    this.checked=false;
                });
            }
        });

	});



	function submitPassword(event, form, dn, pwd1, pwd2, chb1, chb2, butt){
		var request;
		event.preventDefault();
		if($("#"+pwd1).val()!==''&&$("#"+pwd2).val()!==''){
			if($("#"+pwd1).val()!==$("#"+pwd2).val()){
				$("#"+pwd1).val('');
				$("#"+pwd2).val('');
				$("#err_list").empty();
				$("#err_list").append('<li>Hasła się nie zgadzają</li>');
				var x = document.getElementById("errors");
				x.style.display = 'inline-block';

				$("#errors").delay(4200).fadeOut(300);
			}else{

				// Abort any pending request
				if (request) {
					request.abort();
				}
				// setup some local variables
				var $form = $("#"+form);

				// Let's select and cache all the fields
				var $inputs = $form.find("input, select, button, textarea");

				// Serialize the data in the form
				//var serializedData = $form.serialize();


				//Define Data to be sent
				var formData = {
					'password1' : $("#"+pwd1).val(),
					'password2' : $("#"+pwd2).val(),
					'wifi' : $("#"+chb2).prop('checked'),
					'ukp' : $("#"+chb1).prop('checked'),
					'dn' : $("#"+dn).text()
				};

				// Let's disable the inputs for the duration of the Ajax request.
				// Note: we disable elements AFTER the form data has been serialized.
				// Disabled form elements will not be serialized.
				$inputs.prop("disabled", true);


				// Fire off the request to /form.php
				request = $.ajax({
					url: "<?php echo site_url('ajaxChange');?>",
					type: "post",
					data: formData,
					dataType : 'json',
					encode : true
				});

				// Callback handler that will be called on success
				request.done(function (response, textStatus, jqXHR){
					// Erase data from input form
					$("#"+pwd1).val('');
					$("#"+pwd2).val('');
					$("#"+chb1).prop("checked", false);
					$("#"+chb2).prop("checked", false);
					console.log(response);
					$("#err_list").empty();
					for(var i=0;i<response.length;i++){
						$("#err_list").append('<li>'+response[i]+'</li>');
					}
					var x = document.getElementById("errors");
					x.style.display = 'inline-block';

					$("#errors").delay(4200).fadeOut(300);


				});

				// Callback handler that will be called on failure
				request.fail(function (jqXHR, textStatus, errorThrown){
					// Log the error to the console
					console.error(
						"The following error occurred: "+
						textStatus, errorThrown
					);
				});

				// Callback handler that will be called regardless
				// if the request failed or succeeded
				request.always(function () {
					// Reenable the inputs but disable form inputs untill checkboxes are checked
					$inputs.prop("disabled", false);
					$("#"+pwd1).prop('disabled', true);
					$("#"+pwd2).prop('disabled', true);
					$("#"+butt).prop('disabled', true);
				});
			}
		}else{

			$("#err_list").empty();
				$("#err_list").append('<li>Wypełnij obydwa pola</li>');
			var x = document.getElementById("errors");
			x.style.display = 'inline-block';

			$("#errors").delay(4200).fadeOut(300);

		}


	}
	function showHide(id){
		var x = document.getElementById(id);
		if (x.style.display === "none") {
			x.style.display = "table-row";
		} else {
			x.style.display = "none";
		}
	}

	function checkBlock(chb1,chb2,pwd1,pwd2,butt){
		if (($("#"+chb1).not(':checked').length) && ($("#"+chb2).not(':checked').length)) {
			$('#'+pwd1).attr("placeholder", "Nowe hasło");
			$('#'+pwd2).attr("placeholder", "Potwierdż hasło");
			$("#"+pwd1+", #"+pwd2+", #"+butt+"").prop('disabled', true);
			$("#"+pwd1+", #"+pwd2+"").val("");
		} else if ($("#"+chb1).not(':checked').length) {

			$("#"+pwd1+", #"+pwd2+", #"+butt+"").prop('disabled', false);
			$('#'+pwd1).attr("placeholder", "Nowe hasło WI-FI");
			$('#'+pwd2).attr("placeholder", "Potwierdż hasło WI-FI");
		} else if ($("#"+chb2).not(':checked').length) {
			$("#"+pwd1+", #"+pwd2+", #"+butt+"").prop('disabled', false);
			$('#'+pwd1).attr("placeholder", "Nowe hasło UKP(S)");
			$('#'+pwd2).attr("placeholder", "Potwierdż hasło UKP(S)");
		} else {
			$("#"+pwd1+", #"+pwd2+", #"+butt+"").prop('disabled', false);
			$('#'+pwd1).attr("placeholder", "Nowe hasło WI-FI i UKP(S)");
			$('#'+pwd2).attr("placeholder", "Potwierdż hasło WI-FI i UKP(S)");
		}
	}
</script>
</body>
</html>
