<?php
$post = filter_input_array(INPUT_POST);
if(isset($post['submit']) && $post['beitraege_pro_seite'] >0){

	//change gb configuration
    $gb_temp = array();
    $gb_temp['gb_title'] = $post['gb_title'];
    $gb_temp['gb_language'] = $post['language'];
    $gb_temp['view_email_input'] = $post['show_email'];
    $gb_temp['view_hp_input'] = $post['show_homepage'];
    $gb_temp['view_img_url_input'] = $post['show_image'];
    $gb_temp['view_subject_input'] = $post['show_subject'];
    $gb_temp['posts_on_page'] = $post['beitraege_pro_seite'];
    $gb_temp['scale_image'] = $post['scale'];
    $gb_temp['post_instant_online'] = $post['public'];

    //write gb configuration
    $db->updateConfigTable($gb_temp);

    $info = '<p class="alert alert-success">'.$l['settings_successful_saved'].'</p>';
    $gbs = $gb->getGbSettings('commit');
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
					<input type="text" class="form-control" id="gb_title" name="gb_title" placeholder="<?=$l['gb']?>" value="<?php echo $gbs['gb_title']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="show_email" class="col-sm-4 control-label hh_form"><?=$l['email']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_email" name="show_email">
						<option value="1" <?php ($gbs['view_email_input']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($gbs['view_email_input']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="show_homepage" class="col-sm-4 control-label hh_form"><?=$l['homepage']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_homepage" name="show_homepage">
						<option value="1" <?php ($gbs['view_hp_input']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($gbs['view_hp_input']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="show_image" class="col-sm-4 control-label hh_form"><?=$l['image']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_image" name="show_image">
						<option value="1" <?php ($gbs['view_img_url_input']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($gbs['view_img_url_input']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="show_subject" class="col-sm-4 control-label hh_form"><?=$l['subject']?> <?=$l['visibility']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="show_subject" name="show_subject">
						<option value="1" <?php ($gbs['view_subject_input']) ? print 'selected' : print''; ?>><?=$l['visible']?></option>
						<option value="0" <?php ($gbs['view_subject_input']) ? print '' : print 'selected'; ?>><?=$l['invisible']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="beitraege_pro_seite" class="col-sm-4 control-label hh_form"><?=$l['msgs_per_page']?></label>
				<div class="col-sm-8">
					<input type="number" min="5" step="5" class="form-control" id="beitraege_pro_seite" name="beitraege_pro_seite" placeholder="5" value="<?php echo $gbs['posts_on_page']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="public" class="col-sm-4 control-label hh_form"><?=$l['post_msgs_immediately']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="public" name="public">
						<option value="1" <?php ($gbs['post_instant_online']) ? print 'selected' : print''; ?>><?=$l['yes']?></option>
						<option value="0" <?php ($gbs['post_instant_online']) ? print '' : print 'selected'; ?>><?=$l['no']?></option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="hinweis" class="col-sm-4 control-label hh_form"><?=$l['gb'].' '.$l['language']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="language" name="language">
						<?php 
							$lang_file = $gb->getLanguageFileList();
							foreach($lang_file as $key => $value){
						?>
							<option value="<?=$value?>" <?=($gbs['gb_language']==$value)? 'selected': ''?>><?=$value?></option>
							<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="public" class="col-sm-4 control-label hh_form"><?=$l['scale_image']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="public" name="scale">
						<option value="1" <?php ($gbs['scale_image']) ? print 'selected' : print''; ?>><?=$l['yes']?></option>
						<option value="0" <?php ($gbs['scale_image']) ? print '' : print 'selected'; ?>><?=$l['no']?></option>
					</select>
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
