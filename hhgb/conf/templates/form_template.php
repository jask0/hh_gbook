<!-- This is the Template for the submit form, shoud be loaded by showForm() -->
<div class="row">
	<div class="gb-overall col-md-8 col-md-offset-2">
		<?=(isset($_POST['info_msg']))? $_POST['info_msg'] : ''?>
		<form class="form-horizontal" action="<?php echo $data['action']; ?>" method="post" id="myForm">
			<div class="form-group <?=$gbs['highlite_field_color']?> ">
				<label for="name" class="col-sm-2 control-label sr-only custom-form"><?=$l['name']?>*</label>
				<div class="col-sm-12">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-user"></i></span>
						<input type="text" class="form-control jscolor" id="name" name="name" placeholder="<?=$l['name']?>*" value="<?php echo $data['name']; ?>">
					</div>
				</div>
			</div>
			<?php if ($gbs['view_email_input']== "1") { ?>
				<div class="form-group">
					<label for="email" class="col-sm-2 control-label sr-only custom-form"><?=$l['email']?></label>
					<div class="col-sm-12">
						<div class="input-group">
							<span class="input-group-addon">@</span>
							<input type="email" class="form-control" id="email" name="email" placeholder="<?=$l['email']?>" value="<?php echo $data['email']; ?>">
						</div>
					</div>
				</div>
			<?php }
			if ((int)$gbs['view_hp_input']) { ?>
				<div class="form-group">
					<label for="homepage" class="col-sm-2 control-label sr-only custom-form"><?=$l['homepage']?></label>
					<div class="col-sm-12">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-home"></i></span>
							<input type="url" class="form-control" id="homepage" name="homepage" placeholder="<?=$l['homepage']?>" value="<?php echo $data['homepage']; ?>">
						</div>
					</div>
				</div>
			<?php }
			if ((int)$gbs['view_img_url_input']) { ?>
				<div class="form-group">
					<label for="bild" class="col-sm-2 control-label sr-only custom-form"><?=$l['image']?></label>
					<div class="col-sm-12">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-picture-o"></i></span>
							<input type="text" class="form-control" id="bild" name="bild_url" placeholder="<?=$l['image']?>" value="<?php echo $data['bild_url']; ?>">
						</div>
					</div>
				</div>
			<?php }
			if ((int)$gbs['view_subject_input']) { ?>
				<div class="form-group">
					<label for="betreff" class="col-sm-2 control-label sr-only custom-form"><?=$l['subject']?></label>
					<div class="col-sm-12">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-pencil"></i></span>
							<input type="text" class="form-control" id="betreff" name="betreff" placeholder="<?=$l['subject']?>" value="<?php echo $data['betreff']; ?>">
						</div>
					</div>
				</div>
			<?php } 
			if(!isset($_GET['id'])) { ?>
			<div class="form-group">
				<div class="col-sm-12">
					<div class="well well-sm">
						<center>
							<?php
							for ($i = 0; $i < count($smilie['list']); $i++) {
								if ($i % 12 == 0 && $i != 0) echo '<!--<br>-->';
								echo '<a href="javascript:insertTheSmiley(\':'.$smilie['list'][$i].':\', \'nachricht\');void(0)"><img src="'.$path.'hhgb/smilies/'.$smilie['folder'].'/'.$smilie['list'][$i].'" alt=":'.$smilie['folder'].$i.':" ></a>&nbsp;&nbsp;';
							}
							?>
						</center>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="form-group <?=$gbs['highlite_field_color']?> ">
				<label for="nachricht" class="col-sm-2 control-label sr-only custom-form"><?=$l['msg']?>*</label>
				<div class="col-sm-12">
					<textarea class="form-control" name="nachricht" id="nachricht" placeholder="<?=$l['msg']?>*"><?php echo $data['nachricht']; ?></textarea>
				</div>
			</div>
			<?php if(!isset($_GET['id'])) { ?>
			<div class="form-group <?=$gbs['highlite_field_color']?>">
					<label for="captcha" class="col-sm-2 control-label sr-only custom-form"><?=$l['captcha']?></label>
					<div class="col-sm-12">
						<div class="col-sm-3">
							<img src="<?=$path?>hhgb/captcha/captcha.php" class="img-thumbnail">
						</div>
						<div class="col-sm-7">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-lock"></i></span>
								<input type="text" class="form-control" id="captcha" name="captcha" placeholder="<?=$l['captcha']?>*">
							</div>
						</div>
					</div>
				</div>
			<?php }
				if(isset($data['admin']) && $data['admin'] == 1) { ?>
				<div class="form-group">
					<label for="kommentar" class="col-sm-2 control-label sr-only custom-form"><?=$l['comment']?></label>
					<div class="col-sm-12">
						<textarea class="form-control" name="kommentar" id="kommentar" placeholder="<?=$l['comment']?>"><?php echo $data['kommentar']; ?></textarea>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-8 col-md-offset-2">
						<div class="checkbox">
							<label>
								<input type="checkbox" value="1" name="public" <?php ($data['public']) ? print 'checked' : print ''; ?>> <?=$l['publish']?>
							</label>
						</div>
						<div class="checkbox">
							<label>
								<input type="checkbox" value="1" name="loeschen"> <b style="color:red;"><?=$l['delete']?></b>
							</label>
						</div>
					</div>
				</div>
			<?php } ?>

			<div class="form-group">
				<div class="col-sm-12">
					<?php if(!isset($data['admin'])) { ?>
						<p class="<?=$gbs['note_text_color']?> custom-note"><?php echo $l['note_msg']; ?></p>
					<?php } else {?>
						<input type="hidden" value="<?php echo date_format($data['datum'], 'Y-m-d H:i:s'); ?>" name="old_datum">
					<?php } ?>
					<button type="submit" class="btn <?=$gbs['send_btn_color']?>" id="submit" name="submit"><?=$l['send']?></button>
				</div>
			</div>
		</form>
	</div><!-- END .gb-overall -->
</div><!-- END .row -->
