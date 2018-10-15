<div class="app">
	<table class="table-bordered table-striped">
		<thead>
		<th><b>Użytkownik</b></th>
		<th><b>UID</b></th>
		<th><b>Pesel</b></th>
		<th><b>Grupa</b></th>
		</thead>
		<tbody>
		<?php for($i=0;$i<count($person); $i++): ?>
			<tr>
			<td><?=$person[$i]['cn']?></td><td><?=$person[$i]['uid']?></td><td><?=$person[$i]['pesel']?></td><td><?=$person[$i]['objectclass']?></td><td class="tdbutton"><span onclick="showHide('form_<?=$i?>')" class="btn btn-warning"><i class="fa fa-arrows-v"></i></span></td>
		</tr>
		<tr id="form_<?=$i?>"
			<?php if(count($person)==1): ?>
				style="">
			<?php else: ?>
				style="display:none">
			<?php endif; ?>
			<td colspan="4">
				<div class="pcont">
					<form id="form_change_<?=$i?>" onsubmit="submitPassword(event, 'form_change_<?=$i?>','dn_<?=$i?>','password1_<?=$i?>','password2_<?=$i?>', 'chb1_<?=$i?>','chb2_<?=$i?>','change_id_<?=$i?>')">

					<div class="checkbox">
    <label><input type="checkbox" id="chb1_<?=$i?>" onclick="checkBlock('chb1_<?=$i?>','chb2_<?=$i?>','password1_<?=$i?>','password2_<?=$i?>','change_id_<?=$i?>')" > Zmień hasło UKP(S)</label>
  </div><div class="checkbox">
    <label><input type="checkbox" id="chb2_<?=$i?>" onclick="checkBlock('chb1_<?=$i?>','chb2_<?=$i?>','password1_<?=$i?>','password2_<?=$i?>','change_id_<?=$i?>')" > Zmień hasło WI-FI</label>
  </div>
						<div class="form-group">
						    <span class="hidden" id="dn_<?=$i?>"><?=$person[$i]['dn']?></span>
							<label for="exampleInputEmail1">Wpisz nowe hasło</label>
							<input type="password" class="form-control" id="password1_<?=$i?>" name="password1_<?=$i?>" aria-describedby="emailHelp" placeholder="Nowe hasło" disabled>
						</div>
						<div class="form-group">
							<label for="exampleInputPassword1">Powtórz nowe hasło</label>
							<input type="password" class="form-control" name="password2_<?=$i?>" id="password2_<?=$i?>" placeholder="Potwierdż hasło" disabled>
						</div>
						<button id="change_id_<?=$i?>" type="submit" class="btn btn-info" disabled>Zmień</button>
					</form>
				</div>
			</td>
		</tr>
	<?php endfor; ?>


		</tbody>
	</table>
</div>
</div>
