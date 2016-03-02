<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">
			<?=$l['admin_area']?>
			<small><?=$l['use_custom_css']?></small>
		</h1>
	</div>
</div>
<?php
	if(!isset($_SESSION['username'])){
		die($l['prohibited_direct_access']);
	}
	//include('load_settings.php');
	//$sgs = getGBsettings($conn, 1);

	if(isset($_POST['use_css']) && $_POST['use_css'] == 1){
		$jcss = getCustomCss();
		$use_css = 1;
		
		$file = fopen('conf/css/gb.custom.css', 'w');
		$css = '.custom-panel { border-color: #'. $_POST['custom-panel'].';}'."\n";
		$css .= '.panel-primary > .custom-heading { background-color: #'. $_POST['custom-heading'].';}'."\n";
		$css .= '.custom-body { background-color: #'. $_POST['custom-body'].';}'."\n";
		$css .= '.custom-footer { background-color: #'. $_POST['custom-footer'].';}';
		fwrite($file,$css);
		fclose($file);
		
		$jcss['use_custom_css'] = 1;
		$jcss['.custom-panel']['border-color'] = $_POST['custom-panel'];
		$jcss['.custom-heading']['background-color'] = $_POST['custom-heading'];
		$jcss['.custom-body']['background-color'] = $_POST['custom-body'];
		$jcss['.custom-footer']['background-color'] = $_POST['custom-footer'];
		
		$fp = fopen('conf/css/custom.css.json', 'w');
		fwrite($fp, json_encode($jcss));
		fclose($fp);
		
		$info = '<p class="alert alert-success">'.$l['settings_successful_saved'].'</p>';
		
	} else {
		$use_css = 0;
	}
	
	if(isset($_POST['submit'])){
		include 'conf/user.php';
		
		$fp = fopen('conf/user.php', 'w') or die($ERROR_CREATING_FILE);
		$write = '<?php $user = Array(';
		$write .="'username' => '".$user['username']."',";
		$write .="'password' => '".$user['password']."',";
		$write .="'email' => '".$user['email']."',";
		$write .="'mail_msg' => ".$user['mail_msg'].",";
		$write .="'language' => '".$user['language']."',";
		$write .="'custom_css' => '".$use_css."'); ?>";
		fwrite($fp,$write);
		fclose($fp);
		$info = '<p class="alert alert-success">'.$l['settings_successful_saved'].'</p>';
	}
	include 'conf/user.php';
	$css = getCustomCss();
?>

<div class="col-md-12">
	<form class="form-horizontal" action="?page=custom" method="post">
		<div class="col-md-3">
			<div class="form-group">
				<label for="name" class="control-label"><?=$l['header_bg']?></label>
				<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
						<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueHeaderBG', styleElement:'styleHeaderBG', position:'right'}" id="valueHeaderBG" name="custom-heading" value="<?=$css['.custom-heading']['background-color']?>">
				</div>
			</div>
			<div class="form-group">
				<label for="name" class="control-label"><?=$l['text_bg']?></label>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
					<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueTextBG',styleElement:'styleTextBG', position:'right'}" id="valueTextBG" name="custom-body" value="<?=$css['.custom-body']['background-color']?>">
				</div>
			</div>
			<div class="form-group">
				<label for="name" class="control-label"><?=$l['comment_bg']?></label>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
					<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueCommentBG',styleElement:'styleCommentBG', position:'right'}" id="valueCommentBG" name="custom-footer" value="<?=$css['.custom-footer']['background-color']?>">
				</div>
			</div>
		
			<div class="form-group">
				<label for="name" class="control-label"><?=$l['border_color']?></label>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
					<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueBorder',styleElement:'styleBorder',onFineChange:'update(this)', position:'right'}" id="valueBorder" name="custom-panel" value="<?=$css['.custom-panel']['border-color']?>">
				</div>
			</div>
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" value="1" name="use_css" <?=($css['use_custom_css']==1)? 'checked':''?>> <b><?=$l['use_custom_css']?></b>
					</label>
				</div>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-success" id="submit" name="submit"><?=$l['save']?></button>
			</div>
		</div>
	</form>
<div class="gb-overall col-md-9">
		<?=(isset($info))? $info:''; ?>
		<div class="panel panel-primary custom-pannel" id="styleBorder">
			<div class="panel-heading custom-heading" id="styleHeaderBG">
					<a class="btn btn-danger btn-xs pull-right" style="margin-left:5px;"><i class="fa fa-pencil"></i> <?=$l['edit']?></a> 
				by: Dummy
				<div class="pull-right">
						<a href="#" class="btn btn-default btn-xs" title="<?=$l['email']?>">
							<i class="fa fa-envelope-o"></i>
						</a>
						<a href="#" class="btn btn-default btn-xs" title="<?=$l['homepage']?>">
							<i class="fa fa-home"></i>
						</a>
				</div>
			</div>
			<div class="panel-body custom-body" id="styleTextBG">
					<div class="col-xs-6 col-md-3">
						<a href="" class="thumbnail">
							<img src="conf/css/tmp.png" alt="Thumbnail">
						</a>
					</div>
					<h4>Lorem impsum</h4>
					<p>is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
			</div>
				<div class="panel-footer custom-footer" id="styleCommentBG">
					<p>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
				</div>
		</div>
	</div>
</div>
<script>
function update(jscolor) {
    // 'jscolor' instance can be used as a string
    document.getElementById('styleBorder').style.borderColor = '#' + jscolor
}
</script>
<?php echo '<pre>' . print_r($css, true) . '</pre>'; ?>
<?php $css['use_custom_css'] = 0; ?>
<?php echo '<pre>' . print_r($css, true) . '</pre>'; ?>