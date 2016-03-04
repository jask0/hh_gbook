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

	if(isset($_POST['use_css']) && $_POST['use_css'] == 1){
		$jcss = getCustomCss();
		$use_css = 1;
		
		$file = fopen('conf/css/gb.custom.css', 'w');
		$css = '.custom-panel {'."\n\t".'border-color: #'. $_POST['custom-panel'].';}'."\n";
		$css .= '.custom-thumbnail {'."\n\t".'background-color: #'. $_POST['custom-thumbnail'].';'."\n";
		$css .= "\t".'border: 0px solid #'. $_POST['custom-thumbnail'].' !important;}'."\n";
		$css .= '.panel-primary > .custom-heading {'."\n\t".'color: #'. $_POST['custom-heading-txt'].';'."\n";
		$css .= "\t".'background-color: #'. $_POST['custom-heading'].';'."\n";
		$css .= "\t".'border-color: #'. $_POST['custom-panel'].';}'."\n";
		$css .= '.custom-body {'."\n\t".'background-color: #'. $_POST['custom-body'].';'."\n";
		$css .= "\t".'color: #'. $_POST['custom-body-txt'].';}'."\n";
		$css .= '.custom-footer {'."\n\t".'color: #'. $_POST['custom-footer-txt'].';'."\n";
		$css .= "\t".'background-color: #'. $_POST['custom-footer'].';';
		$css .= "\t".'border-top: 1px solid #'. $_POST['custom-panel'].' !important;}';
		fwrite($file,$css);
		fclose($file);
		
		$jcss['use_custom_css'] = 1;
		$jcss['.custom-panel']['border-color'] = $_POST['custom-panel'];
		$jcss['.custom-thumbnail']['background-color'] = $_POST['custom-thumbnail'];
		$jcss['.custom-thumbnail']['border-color'] = $_POST['custom-thumbnail'];
		$jcss['.custom-heading']['background-color'] = $_POST['custom-heading'];
		$jcss['.custom-heading']['color'] = $_POST['custom-heading-txt'];
		$jcss['.custom-body']['background-color'] = $_POST['custom-body'];
		$jcss['.custom-body']['color'] = $_POST['custom-body-txt'];
		$jcss['.custom-footer']['background-color'] = $_POST['custom-footer'];
		$jcss['.custom-footer']['color'] = $_POST['custom-footer-txt'];
		
		$fp = fopen('conf/custom.css.json', 'w');
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
<form class="form-horizontal" action="?page=custom" method="post">
	<div class="col-md-12">
		
			<div class="col-md-2">
				<div class="form-group">
					<label for="name" class="control-label"><?=$l['header_bg']?></label>
					<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
							<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueHeaderBG', styleElement:'styleHeaderBG', position:'right'}" id="valueHeaderBG" name="custom-heading" value="<?=$css['.custom-heading']['background-color']?>">
					</div>
				</div>
				<div class="form-group">
					<label for="name" class="control-label">Header Textfarbe</label>
					<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
							<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueHeaderTXT', styleElement:'style001', onFineChange:'update(this,1)', position:'right'}" id="valueHeaderTXT" name="custom-heading-txt" value="<?=$css['.custom-heading']['color']?>">
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
					<label for="name" class="control-label">Body Textfarbe</label>
					<div class="input-group">
							<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
							<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueTextTXT', styleElement:'style001', onFineChange:'update(this,2)', position:'right'}" id="valueTextTXT" name="custom-body-txt" value="<?=$css['.custom-body']['color']?>">
					</div>
				</div>
			</div>
	<div class="gb-overall col-md-8">
			<?=(isset($info))? $info:''; ?>
			<div class="panel panel-primary custom-panel" id="styleBorder">
				<div class="panel-heading custom-heading" id="styleHeaderBG">
						<a class="btn btn-danger btn-xs pull-right" style="margin-left:5px;"><i class="fa fa-pencil"></i> <?=$l['edit']?></a> 
					<span id="styleHeaderTXT">by: Dummy</span>
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
							<a href="" class="thumbnail" id="styleThumbBorder" style="border: 0 !important;">
								<img src="conf/css/tmp.png" alt="Thumbnail" onload="color()">
							</a>
						</div>
						<span id="styleTextTXT">
							<h4>Lorem impsum</h4>
							<p>is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
						</span>
				</div>
					<div class="panel-footer custom-footer" id="styleCommentBG">
						<p id="styleCommentTXT">It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>
					</div>
			</div>
			<div class="form-group">
				<div class="col-md-6 offset-col-md-2">
					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" name="use_css" <?=($css['use_custom_css']==1)? 'checked':''?>> <b><?=$l['use_custom_css']?></b>
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-3 offset-col-md-2">
					<button type="submit" class="btn btn-success" id="submit" name="submit"><?=$l['save']?></button>
				</div>
			</div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label for="name" class="control-label"><?=$l['comment_bg']?></label>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
					<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueCommentBG',styleElement:'styleCommentBG', position:'right'}" id="valueCommentBG" name="custom-footer" value="<?=$css['.custom-footer']['background-color']?>">
				</div>
			</div>
			<div class="form-group">
				<label for="name" class="control-label">Kommentar Textfarbe</label>
				<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
						<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueCommentTXT',styleElement:'style001', onFineChange:'update(this,3)', position:'right'}" id="valueCommentTXT" name="custom-footer-txt" value="<?=$css['.custom-footer']['color']?>">
				</div>
			</div>
			<div class="form-group">
				<label for="name" class="control-label"><?=$l['border_color']?></label>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
					<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueBorder',styleElement:'styleBorder',onFineChange:'update(this,0)', position:'right'}" id="valueBorder" name="custom-panel" value="<?=$css['.custom-panel']['border-color']?>">
				</div>
			</div>
			<div class="form-group">
				<label for="valueThumbBorder" class="control-label">Thumbnail Rahmenfarbe</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
					<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueThumbBorder',styleElement:'styleThumbBorder', onFineChange:'update(this,4)', position:'right'}" id="valueThumbBorder" name="custom-thumbnail" value="<?=$css['.custom-thumbnail']['border-color']?>">
				</div>
			</div>
		</div>
	</div>
</form>
<script>
function update(jscolor, id) {
    // 'jscolor' instance can be used as a string
    if(id==0)
		document.getElementById('styleBorder').style.borderColor = '#' + jscolor;
	if(id==1)
		document.getElementById('styleHeaderTXT').style.color = '#' + jscolor;
	if(id==2)
		document.getElementById('styleTextTXT').style.color = '#' + jscolor;
	if(id==3)
		document.getElementById('styleCommentTXT').style.color = '#' + jscolor;
	if(id==4)
		document.getElementById('styleThumbBorder').style.borderColor = '#' + jscolor;
}
function color(id) {
    // ***
	document.getElementById('styleBorder').style.borderColor = '#<?=$css['.custom-panel']['border-color']?>';
	document.getElementById('styleThumbBorder').style.borderColor = '#<?=$css['.custom-panel']['border-color']?>';
	document.getElementById('styleHeaderTXT').style.color = '#<?=$css['.custom-heading']['color']?>';
	document.getElementById('styleTextTXT').style.color = '#<?=$css['.custom-body']['color']?>';
	document.getElementById('styleCommentTXT').style.color = '#<?=$css['.custom-footer']['color']?>';
}
</script>