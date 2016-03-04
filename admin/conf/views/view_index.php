<?php
if($_POST && $_POST['beitraege_pro_seite'] >0){
	
	$q = "UPDATE hh_gbsettings SET title='".$_POST['gb_title']."', email='".$_POST['show_email'];
	$q .="', homepage='".$_POST['show_homepage']."', image='".$_POST['show_image']."', subject='".$_POST['show_subject'];
	$q .="', posts='".$_POST['beitraege_pro_seite']."', public='".$_POST['public']."', msg='".$_POST['msg']."',";
	$q .=" error='".$_POST['fehler']."' WHERE id = 1;";
	
	$r = mysqli_query($conn, $q);
	$info = '<p class="alert alert-success">'.$l['settings_successful_saved'].'</p>';
	$sgs = getGBsettings($conn,1);
}
?>
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">
			<?=$l['admin_area']?>
			<small><?=$l['settings']?></small>
		</h1>
	</div>
</div>
<div class="raw">
	<div class="col-md-12">
		<?php if($_POST) {echo $info;} ?>
		<form class="form-horizontal" action="index.php" method="post" autocomplete="off">
			<div class="form-group">
				<label for="gb_title" class="col-sm-4 control-label hh_form"><?=$l['gb_title']?></label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="gb_title" name="gb_title" placeholder="<?=$l['gb']?>" value="<?php echo $sgs['title']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="show_email" class="col-sm-4 control-label hh_form"><?=$l['email']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_email" name="show_email">
						<option value="1" <?php ($sgs['email']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($sgs['email']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="show_homepage" class="col-sm-4 control-label hh_form"><?=$l['homepage']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_homepage" name="show_homepage">
						<option value="1" <?php ($sgs['homepage']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($sgs['homepage']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="show_image" class="col-sm-4 control-label hh_form"><?=$l['image']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_image" name="show_image">
						<option value="1" <?php ($sgs['image']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($sgs['image']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="show_subject" class="col-sm-4 control-label hh_form"><?=$l['subject']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_subject" name="show_subject">
						<option value="1" <?php ($sgs['subject']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($sgs['subject']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="beitraege_pro_seite" class="col-sm-4 control-label hh_form"><?=$l['msgs_per_page']?></label>
				<div class="col-sm-8">
					<input type="number" min="5" step="5" class="form-control" id="beitraege_pro_seite" name="beitraege_pro_seite" placeholder="5" value="<?php echo $sgs['posts']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="public" class="col-sm-4 control-label hh_form"><?=$l['post_msgs_immediately']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="public" name="public">
						<option value="1" <?php ($sgs['public']) ? print 'selected' : print''; ?>><?=$l['yes']?></option>
						<option value="0" <?php ($sgs['public']) ? print '' : print 'selected'; ?>><?=$l['no']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="hinweis" class="col-sm-4 control-label hh_form"><?=$l['note']?></label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="hinweis" name="msg" placeholder="Hinweis" value="<?php echo $sgs['msg']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="fehler" class="col-sm-4 control-label hh_form"><?=$l['error_msg']?></label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="fehler" name="fehler" placeholder="Fehler" value="<?php echo $sgs['error']; ?>">
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-8">
					<button type="submit" class="btn btn-success" name="submit"><?=$l['save']?></button>
				</div>
			</div>
		</form>
	</div>
</div> <!-- END .raw -->