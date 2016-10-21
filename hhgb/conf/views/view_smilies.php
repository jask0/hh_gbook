<?php
	if(!isset($_SESSION['username'])){
		die($l['prohibited_direct_access']);
	}
	//stored smilie data
	$smilie = $gb->smilie;
	$set = $smilie['folder'];
	
	$smilie_sets = Array();
	$dirs = array_filter(glob('smilies/*'), 'is_dir');
	foreach ($dirs as $key => $value){
		$temp = explode("/", $value);
		array_push($smilie_sets, $temp[1]);
	}
	$smilie_list = Array();
	$post = filter_input_array(INPUT_POST);
	if(!isset($post['submit'])){
		$dirs = array_filter(glob('smilies/'.$smilie['folder'].'/*'));
		foreach ($dirs as $key => $value){
			$temp = explode($smilie['folder']."/", $value);
			array_push($smilie_list, $temp[1]);
		}
	}
	
	if(isset($post['submit'])){
		
		$dirs = array_filter(glob('smilies/'.$post['folder'].'/*'));
		foreach ($dirs as $key => $value){
			$temp = explode($post['folder']."/", $value);
			array_push($smilie_list, $temp[1]);
		}
		
		//change user configuration
		$smilie['folder'] = $_POST['folder'];
		$smilie['list'] = $smilie_list;

		//write user configuration
		$fp = fopen('conf/smilies.json', 'w');
		fwrite($fp, json_encode($smilie));
		fclose($fp);
		
		$info = '<p class="alert alert-success">'.$l['settings_successful_saved'].$l['reload_page_to_view_result'].'</p>';
		
		$set = $post['folder'];
}
?>
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">
			<?=$l['admin_area']?>
			<small><?=$l['smilie_set']?></small>
		</h1>
	</div>
</div>
<div class="raw">
	<div class="col-md-12">
		<?php if($post) {echo $info;} ?>
		<form class="form-horizontal" action="index.php?page=smilies" method="post" autocomplete="off">
			<div class="form-group">
				<label for="set" class="col-sm-4 control-label hh_form"><?=$l['smilie_set']?></label>
				<div class="col-sm-8">
					<select type="text" class="form-control" id="set" name="folder">
						<?php foreach ($smilie_sets as $key => $dir){ ?>
							<option value="<?=$dir?>" <?=($smilie['folder']==$dir) ?'selected':''?>><?=$dir?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="email" class="col-sm-4 control-label hh_form"><?=$l['smilies']?></label>
				<div class="col-sm-8">
						<div class="well well-sm"><?php
							foreach ($smilie_list as $key => $file){
								echo '<img src="smilies/'.$set.'/'.$file.'"> ';
							}
						?></div>
				</div>
			</div>

			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-8">
					<button type="submit" class="btn btn-success" name="submit"><?=$l['save']?></button>
				</div>
			</div>
		</form>
	</div>
</div>
