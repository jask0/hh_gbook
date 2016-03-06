<?php
$gbs = getGBsettings();
if($_POST && $_POST['beitraege_pro_seite'] >0){
	$gb = array();
	$id = $gbs['id'];
	$gb[$id] = array();
	//change gb configuration
	$gb[$id]['id'] = $id;
	$gb[$id]['title'] = $_POST['gb_title'];
	$gb[$id]['language'] = $_POST['language'];
	$gb[$id]['email'] = $_POST['show_email'];
	$gb[$id]['homepage'] = $_POST['show_homepage'];
	$gb[$id]['image'] = $_POST['show_image'];
	$gb[$id]['subject'] = $_POST['show_subject'];
	$gb[$id]['posts'] = $_POST['beitraege_pro_seite'];
	$gb[$id]['public'] = $_POST['public'];

	//write gb configuration
	$fp = fopen('conf/gb.json', 'w');
	fwrite($fp, json_encode($gb));
	fclose($fp);
		
	$info = '<p class="alert alert-success">'.$l['settings_successful_saved'].'</p>';
	$gbs = getGBsettings();
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
		<form class="form-horizontal" action="?page=settings" method="post" autocomplete="off">
			<div class="form-group">
				<label for="gb_title" class="col-sm-4 control-label hh_form"><?=$l['gb_title']?></label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="gb_title" name="gb_title" placeholder="<?=$l['gb']?>" value="<?php echo $gbs['title']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="show_email" class="col-sm-4 control-label hh_form"><?=$l['email']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_email" name="show_email">
						<option value="1" <?php ($gbs['email']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($gbs['email']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="show_homepage" class="col-sm-4 control-label hh_form"><?=$l['homepage']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_homepage" name="show_homepage">
						<option value="1" <?php ($gbs['homepage']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($gbs['homepage']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="show_image" class="col-sm-4 control-label hh_form"><?=$l['image']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_image" name="show_image">
						<option value="1" <?php ($gbs['image']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($gbs['image']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="show_subject" class="col-sm-4 control-label hh_form"><?=$l['subject']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_subject" name="show_subject">
						<option value="1" <?php ($gbs['subject']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($gbs['subject']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="beitraege_pro_seite" class="col-sm-4 control-label hh_form"><?=$l['msgs_per_page']?></label>
				<div class="col-sm-8">
					<input type="number" min="5" step="5" class="form-control" id="beitraege_pro_seite" name="beitraege_pro_seite" placeholder="5" value="<?php echo $gbs['posts']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="public" class="col-sm-4 control-label hh_form"><?=$l['post_msgs_immediately']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="public" name="public">
						<option value="1" <?php ($gbs['public']) ? print 'selected' : print''; ?>><?=$l['yes']?></option>
						<option value="0" <?php ($gbs['public']) ? print '' : print 'selected'; ?>><?=$l['no']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="hinweis" class="col-sm-4 control-label hh_form"><?=$l['gb'].' '.$l['language']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="language" name="language">
						<?php 
							$lang_file = getLanguageFiles();
							foreach($lang_file as $key => $value){
						?>
							<option value="<?=$value?>" <?=($gbs['language']==$value)? 'selected': ''?>><?=$value?></option>
							<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="id" class="col-sm-4 control-label hh_form"><?=$l['gb']?> ID</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="id" name="id" placeholder="ID" value="<?php echo $gbs['id']; ?>" disabled>
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