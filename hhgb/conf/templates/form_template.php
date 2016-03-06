<!-- This is the Template for the submit form, shoud be loaded by showForm() -->
<?php global $path; ?>
<div class="row">
	<div class="gb-overall col-md-8 col-md-offset-2">
		<?=(isset($_POST['info_msg']))? $_POST['info_msg'] : ''?>
		<form class="form-horizontal" action="<?php echo $data['action']; ?>" method="post" id="myForm">
			<div class="form-group has-success ">
				<label for="name" class="col-sm-2 control-label sr-only hh_form"><?=$l['name']?>*</label>
				<div class="col-sm-12">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-user"></i></span>
						<input type="text" class="form-control jscolor" id="name" name="name" placeholder="<?=$l['name']?>*" value="<?php echo $data['name']; ?>">
					</div>
				</div>
			</div>
			<?php if ($gbs['email']) { ?>
				<div class="form-group">
					<label for="email" class="col-sm-2 control-label sr-only hh_form"><?=$l['email']?></label>
					<div class="col-sm-12">
						<div class="input-group">
							<span class="input-group-addon">@</span>
							<input type="email" class="form-control" id="email" name="email" placeholder="<?=$l['email']?>" value="<?php echo $data['email']; ?>">
						</div>
					</div>
				</div>
			<?php }
			if ($gbs['homepage']) { ?>
				<div class="form-group">
					<label for="homepage" class="col-sm-2 control-label sr-only hh_form"><?=$l['homepage']?></label>
					<div class="col-sm-12">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-home"></i></span>
							<input type="text" class="form-control" id="homepage" name="homepage" placeholder="<?=$l['homepage']?>" value="<?php echo $data['homepage']; ?>">
						</div>
					</div>
				</div>
			<?php }
			if ($gbs['image']) { ?>
				<div class="form-group">
					<label for="bild" class="col-sm-2 control-label sr-only hh_form"><?=$l['image']?></label>
					<div class="col-sm-12">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-picture-o"></i></span>
							<input type="text" class="form-control" id="bild" name="bild_url" placeholder="<?=$l['image']?>" value="<?php echo $data['bild_url']; ?>">
						</div>
					</div>
				</div>
			<?php }
			if ($gbs['subject']) { ?>
				<div class="form-group">
					<label for="betreff" class="col-sm-2 control-label sr-only hh_form"><?=$l['subject']?></label>
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
								echo '<a href="javascript:insertTheSmiley(\':'.$smilie['list'][$i].':\', \'nachricht\');void(0)"><img src="'.$path.'hhgb/smilies/'.$smilie['set'].'/'.$smilie['list'][$i].'" alt=":'.$smilie['set'].$i.':" ></a>&nbsp;&nbsp;';
							}
							?>
						</center>
					</div>
				</div>
			</div>
			<?php } ?>
			<div class="form-group has-success ">
				<label for="nachricht" class="col-sm-2 control-label sr-only hh_form"><?=$l['msg']?>*</label>
				<div class="col-sm-12">
					<textarea class="form-control" name="nachricht" id="nachricht" placeholder="<?=$l['msg']?>*"><?php echo $data['nachricht']; ?></textarea>
				</div>
			</div>
			<?php if(!isset($_GET['id'])) { ?>
			<div class="form-group has-success">
					<label for="captcha" class="col-sm-2 control-label sr-only hh_form"><?=$l['captcha']?></label>
					<div class="col-sm-12">
						<div class="col-sm-3">
							<img src="<?=$path?>hhgb/captcha/captcha.php" class="img-thumbnail">
						</div>
						<div class="col-sm-7">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-lock"></i></span>
								<input type="text" class="form-control" id="captcha" name="captcha" placeholder="<?=$l['captcha']?>">
							</div>
						</div>
					</div>
				</div>
			<?php }
				if(isset($data['admin']) && $data['admin'] == 1) { ?>
				<div class="form-group">
					<label for="kommentar" class="col-sm-2 control-label sr-only hh_form"><?=$l['comment']?></label>
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
						<input type="hidden" value="<?php echo $gbs['public']; ?>" name="public">
						<input type="hidden" value="<?php echo $gbs['id']; ?>" name="gbid">
						<p class="text-success"><?php echo $l['note_msg']; ?></p>
					<?php } ?>
					<button type="submit" class="btn btn-success" id="submit" name="submit"><?=$l['send']?></button>
				</div>
			</div>
		</form>
	</div><!-- END .gb-overall -->
</div><!-- END .row -->