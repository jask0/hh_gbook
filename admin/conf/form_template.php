<!-- This is the Template for the submit form, shoud be loaded by showForm() -->
<div class="gb-overall col-md-8 col-md-offset-2">
	<form class="form-horizontal" action="<?php echo $data['action']; ?>" method="post" id="myForm">
		<div class="form-group has-success ">
			<label for="name" class="col-sm-2 control-label sr-only hh_form">Name*</label>
			<div class="col-sm-12">
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-user"></i></span>
					<input type="text" class="form-control" id="name" name="name" placeholder="Name*" value="<?php echo $data['name']; ?>">
				</div>
			</div>
		</div>
		<?php if ($sgs['email']) { ?>
			<div class="form-group">
				<label for="email" class="col-sm-2 control-label sr-only hh_form">E-Mail</label>
				<div class="col-sm-12">
					<div class="input-group">
						<span class="input-group-addon">@</span>
						<input type="email" class="form-control" id="email" name="email" placeholder="E-Mail" value="<?php echo $data['email']; ?>">
					</div>
				</div>
			</div>
		<?php }
		if ($sgs['homepage']) { ?>
			<div class="form-group">
				<label for="homepage" class="col-sm-2 control-label sr-only hh_form">Homepage</label>
				<div class="col-sm-12">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-home"></i></span>
						<input type="text" class="form-control" id="homepage" name="homepage" placeholder="Homepage" value="<?php echo $data['homepage']; ?>">
					</div>
				</div>
			</div>
		<?php }
		if ($sgs['image']) { ?>
			<div class="form-group">
				<label for="bild" class="col-sm-2 control-label sr-only hh_form">Bild</label>
				<div class="col-sm-12">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-picture-o"></i></span>
						<input type="text" class="form-control" id="bild" name="bild_url" placeholder="Bild-URL" value="<?php echo $data['bild_url']; ?>">
					</div>
				</div>
			</div>
		<?php }
		if ($sgs['subject']) { ?>
			<div class="form-group">
				<label for="betreff" class="col-sm-2 control-label sr-only hh_form">Betreff</label>
				<div class="col-sm-12">
					<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-pencil"></i></span>
						<input type="text" class="form-control" id="betreff" name="betreff" placeholder="Betreff" value="<?php echo $data['betreff']; ?>">
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
							if ($i % 12 == 0 && $i != 0) echo '<br>';
							echo '<a href="javascript:insertTheSmiley(\':'.$smilie['list'][$i].':\', \'nachricht\');void(0)"><img src="admin/smilies/'.$smilie['set'].'/icon_'.$smilie['list'][$i].'.'.$smilie['ext'].'" alt=":'.$smilie['list'][$i].':" ></a>&nbsp;&nbsp;';
						}
						?>
					</center>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="form-group has-success ">
			<label for="nachricht" class="col-sm-2 control-label sr-only hh_form">Nachricht*</label>
			<div class="col-sm-12">
				<textarea class="form-control" name="nachricht" id="nachricht" placeholder="Nachricht"><?php echo $data['nachricht']; ?></textarea>
			</div>
		</div>
		<?php if(!isset($_GET['id'])) { ?>
		<div class="form-group has-success">
				<label for="captcha" class="col-sm-2 control-label sr-only hh_form">Sicherheitscode</label>
				<div class="col-sm-12">
					<div class="col-sm-3">
						<img src="admin/captcha/captcha.php" class="img-thumbnail">
					</div>
					<div class="col-sm-7">
						<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-lock"></i></span>
							<input type="text" class="form-control" id="captcha" name="captcha" placeholder="Sicherheitscode">
						</div>
					</div>
				</div>
			</div>
		<?php }
			if(isset($data['admin']) && $data['admin'] == 1) { ?>
			<div class="form-group">
				<label for="kommentar" class="col-sm-2 control-label sr-only hh_form">Kommentar</label>
				<div class="col-sm-12">
					<textarea class="form-control" name="kommentar" id="kommentar" placeholder="Kommentar"><?php echo $data['kommentar']; ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-8 col-md-offset-2">
					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" name="public" <?php ($data['public']) ? print 'checked' : print ''; ?>> Ver&ouml;ffentlichen
						</label>
					</div>
					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" name="loeschen"> <b style="color:red;">Jetzt l&ouml;schen</b>
						</label>
					</div>
				</div>
			</div>
		<?php } ?>

		<div class="form-group">
			<div class="col-sm-12">
				<?php if(!isset($data['admin'])) { ?>
					<input type="hidden" value="<?php echo $sgs['public']; ?>" name="public">
					<input type="hidden" value="<?php echo $sgs['id']; ?>" name="gbid">
					<p class="text-success"><?php echo $sgs['msg']; ?></p>
				<?php } ?>
				<button type="submit" class="btn btn-success" id="submit" name="submit">Senden</button>
			</div>
		</div>
	</form>
</div>