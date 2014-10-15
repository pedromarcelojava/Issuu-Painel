<h1><?php _e('Document', ISSUU_PAINEL_DOMAIN_LANG); ?></h1>
<form action="" method="post" id="document-upload" enctype="multipart/form-data">
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="file"><?php _e('File', ISSUU_PAINEL_DOMAIN_LANG); ?></label></th>
				<td>
					<input type="file" name="file" id="file">
				</td>
			</tr>
			<tr>
				<th><label for="title"><?php _e('Title', ISSUU_PAINEL_DOMAIN_LANG); ?></label></th>
				<td><input type="text" name="title" id="title" class="regular-text code"></td>
			</tr>
			<tr>
				<th><label for="name"><?php _e('Name in URL', ISSUU_PAINEL_DOMAIN_LANG); ?></label></th>
				<td>
					<input type="text" name="name" id="name" class="regular-text code">
					<p class="description">
						<?php _e('Name that is entered in the URL: http://issuu.com/(username)/docs/(name).<br>Use only lowercase letters [a-z], numbers [0-9] and/or other characters [_.-]. Do not use spaces.<br><strong>NOTE:</strong> If you do not enter a value, it will automatically be generated', ISSUU_PAINEL_DOMAIN_LANG); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label for="description"><?php _e('Description', ISSUU_PAINEL_DOMAIN_LANG); ?></label></th>
				<td>
					<textarea name="description" id="description" cols="45" rows="6"></textarea>
				</td>
			</tr>
			<tr>
				<th><label for="tags">Tags</label></th>
				<td>
					<textarea name="tags" id="tags" cols="45" rows="6"></textarea>
					<p class="description">
						<?php _e('Use commas to separate tags. Do not use spaces.', ISSUU_PAINEL_DOMAIN_LANG); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label><?php _e('Publish date', ISSUU_PAINEL_DOMAIN_LANG); ?></label></th>
				<td>
					<input type="text" name="pub[day]" id="dia" placeholder="<?php _e('Day', ISSUU_PAINEL_DOMAIN_LANG); ?>" class="small-text"
						maxlength="2"> /
					<input type="text" name="pub[month]" id="mes" placeholder="<?php _e('Month', ISSUU_PAINEL_DOMAIN_LANG); ?>" class="small-text"
						maxlength="2"> /
					<input type="text" name="pub[year]" id="ano" placeholder="<?php _e('Year', ISSUU_PAINEL_DOMAIN_LANG); ?>" class="small-text"
						maxlength="4">
					<p class="description">
						<?php _e('Date of publication of the document.<br><strong>NOTE:</strong> If you do not enter a value, the current date will be used', ISSUU_PAINEL_DOMAIN_LANG); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label><?php _e('Folders', ISSUU_PAINEL_DOMAIN_LANG); ?></label></th>
				<td>
					<?php if (isset($folders['folder']) && !empty($folders['folder'])) : ?>
						<fieldset>
							<?php for ($i = 0; $i < $cnt_f; $i++) : ?>
								<label for="folder<?= $i + 1; ?>">
									<input id="folder<?= $i + 1; ?>" type="checkbox" name="folder[]" value="<?= $folders['folder'][$i]->folderId; ?>">
									<?= $folders['folder'][$i]->name; ?> (<?= $folders['folder'][$i]->items; ?>)
								</label><br>
							<?php endfor; ?>
						</fieldset>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th><label for="commentsAllowed"><?php _e('Allow comments', ISSUU_PAINEL_DOMAIN_LANG); ?></label></th>
				<td><input type="checkbox" name="commentsAllowed" id="commentsAllowed" value="true"></td>
			</tr>
			<tr>
				<th><label for="downloadable"><?php _e('Allow file download', ISSUU_PAINEL_DOMAIN_LANG); ?></label></th>
				<td><input type="checkbox" name="downloadable" id="downloadable" value="true"></td>
			</tr>
			<tr>
				<th><label><?php _e('Access', ISSUU_PAINEL_DOMAIN_LANG); ?></label></th>
				<td>
					<fieldset>
						<label for="acesso1">
							<input type="radio" name="access" id="acesso1" value="public">
							<?php _e('Public', ISSUU_PAINEL_DOMAIN_LANG); ?>
						</label><br>
						<label for="acesso2">
							<input type="radio" name="access" id="acesso2" value="private">
							<?php _e('Private', ISSUU_PAINEL_DOMAIN_LANG); ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th>
					<input type="submit" class="button-primary" value="<?php _e('Save', ISSUU_PAINEL_DOMAIN_LANG); ?>">
					<h3>
						<a href="admin.php?page=issuu-document-admin" style="text-decoration: none;">
							<?php _e('Back', ISSUU_PAINEL_DOMAIN_LANG); ?>
						</a>
					</h3>
				</th>
			</tr>
		</tbody>
	</table>
</form>
<script type="text/javascript" charset="utf-8">
	(function($){
		function refreshNumbers()
		{
			var dia = $('#dia').val();
			var mes = $('#mes').val();
			var ano = $('#ano').val();

			var maxDia = 31;

			if (mes != '')
			{
				mes = parseInt(mes);

				if (mes < 0)
				{
					mes = 1;
					$('#mes').val(mes);
				}
				else if (mes > 12)
				{
					mes = 12;
					$('#mes').val(mes);
				}

				if (mes != 2)
				{
					if (mes <= 7)
					{
						if (mes % 2 == 0)
						{
							maxDia = 30;
						}
						else
						{
							maxDia = 31;
						}
					}
					else
					{
						if (mes % 2 == 0)
						{
							maxDia = 31;
						}
						else
						{
							maxDia = 30;
						}
					}
				}
				else
				{
					if (ano != '')
					{
						if (ano.length == 4)
						{
							ano = parseInt(ano);

							if (ano % 4 == 0 && ano % 100 != 0 || (ano % 400 == 0))
							{
								maxDia = 29;
							}
							else
							{
								maxDia = 28;
							}
						}
					}
					else
					{
						if (ano.length == 4)
						{
							maxDia = 28;
						}
					}
				}
			}
			else
			{
				maxDia = 31;
			}

			if (dia != '')
			{
				dia = parseInt(dia);

				if (dia < 0)
				{
					$('#dia').val(1);
				}
				else if(dia > maxDia)
				{
					$('#dia').val(maxDia);
				}
			}
		}
		function wholeNumber(e)
		{
			refreshNumbers();

			if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
			{
				return false;
			}
		}
		$('.small-text').keypress(wholeNumber);
		$('#document-upload').submit(function(e){
			if ($('#file').val() == "")
			{
				alert("Insira um documento");
				return false;
			}
		});
	})(jQuery);
</script>