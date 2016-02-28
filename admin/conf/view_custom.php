<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">
			Administration
			<small>Eigenes CSS nutzen</small>
		</h1>
	</div>
</div>
<?php
	if(!isset($_SESSION['username'])){
		die('Direkter Zugang nicht erlaubt! Bitte einloggen!');
	}
	//include('load_settings.php');
	//$sgs = getGBsettings($conn, 1);
	
	if(isset($_POST['use_css']) && $_POST['use_css'] == 1){
		
		$file = fopen('conf/css/gb.custom.css', 'w');
		$css = '.custom-panel { border-color: #'. $_POST['custom-panel'].' !important;}'."\n";
		$css .= '.custom-heading { background-color: #'. $_POST['custom-heading'].' !important;}'."\n";
		$css .= '.custom-body { background-color: #'. $_POST['custom-body'].';}'."\n";
		$css .= '.custom-footer { background-color: #'. $_POST['custom-footer'].';}';
		
		fwrite($file,$css);
		fclose($file);
		$use_css = 1;
		$info = '<p class="alert alert-success">Die Einstellungen wurden erfolgreich bearbeitet!</p>';
		
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
		$write .="'custom_css' => '".$use_css."'); ?>";
		fwrite($fp,$write);
		fclose($fp);
		$info = '<p class="alert alert-success">Die Einstellungen wurden erfolgreich bearbeitet!</p>';
	}
	include 'conf/user.php';
?>

<div class="col-md-12">
	<form class="form-horizontal" action="?page=custom" method="post">
		<div class="col-md-3">
			<div class="form-group">
				<label for="name" class="control-label">Header Hintergrund</label>
				<div class="input-group">
						<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
						<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueHeaderBG', styleElement:'styleHeaderBG', position:'right'}" id="valueHeaderBG" name="custom-heading" value="337ab7">
				</div>
			</div>
			<div class="form-group">
				<label for="name" class="control-label">Text Hintergrund</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
					<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueTextBG',styleElement:'styleTextBG', position:'right'}" id="valueTextBG" name="custom-body" value="ffffff">
				</div>
			</div>
			<div class="form-group">
				<label for="name" class="control-label">Kommentar Hintergrund</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
					<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueCommentBG',styleElement:'styleCommentBG', position:'right'}" id="valueCommentBG" name="custom-footer" value="f5f5f5">
				</div>
			</div>
		
			<div class="form-group">
				<label for="name" class="control-label">Rahmen</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-hashtag"></i></span>
					<input type="text" class="form-control jscolor" data-jscolor="{valueElement:'valueBorder',styleElement:'styleBorder',onFineChange:'update(this)', position:'right'}" id="valueBorder" name="custom-panel" value="337ab7">
				</div>
			</div>
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" value="1" name="use_css" <?=($user['custom_css']==1)? 'checked':''?>> <b>Eigenes CSS nutzen</b>
					</label>
				</div>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-success" id="submit" name="submit">Speichern</button>
			</div>
		</div>
	</form>
<div class="gb-overall col-md-9">
		<?=(isset($info))? $info:''; ?>
		<div class="panel panel-primary custom-pannel" id="styleBorder">
			<div class="panel-heading custom-heading" id="styleHeaderBG">
					<a class="btn btn-danger btn-xs pull-right" style="margin-left:5px;"><i class="fa fa-pencil"></i> Bearbeiten</a> 
				Von: Dummy
				<div class="pull-right">
						<a href="#" class="btn btn-default btn-xs" title="E-Mail">
							<i class="fa fa-envelope-o"></i>
						</a>
						<a href="#" class="btn btn-default btn-xs" title="Homepage">
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
					<p>ist ein einfacher Demo-Text f&uuml;r die Print- und Schriftindustrie. Lorem Ipsum ist in der Industrie bereits der Standard Demo-Text seit 1500, als ein unbekannter Schriftsteller eine Hand voll W&ouml;rter nahm und diese durcheinander warf um ein Musterbuch zu erstellen. Es hat nicht nur 5 Jahrhunderte &uuml;berlebt, sondern auch in Spruch in die elektronische Schriftbearbeitung geschafft (bemerke, nahezu unver&auml;ndert).</p>
			</div>
				<div class="panel-footer custom-footer" id="styleCommentBG">
					<p>Bekannt wurde es 1960, mit dem erscheinen von "Letraset", welches Passagen von Lorem Ipsum enhielt, so wie Desktop Software wie "Aldus PageMaker" - ebenfalls mit Lorem Ipsum.</p>
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