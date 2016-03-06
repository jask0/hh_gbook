<?php
	if(!isset($_SESSION['username'])){
		die($l['prohibited_direct_access']);
	}
	//stored user data
	global $user;
	
	if($_POST){
		
		//change user password
		$new_pword = $user['password'];
		if($_POST['pword'] != ''){
			if($_POST['pword'] == $_POST['pwordv']){
				$new_pword = md5($_POST['pwordv']);
			}else {
				$info = '<p class="alert alert-danger">'.$l['pasword_error'].' '.$l['settings_not_saved'].'</p>';
			}
		}
		
		//email me about new post if $mail_msg == 1
		if(isset($_POST['mail_msg'])){
			$mail_msg = 1;
		} else {
			$mail_msg = 0;
		}
		
		//change user configuration
		$user['username'] = $_POST['uname'];
		$user['password'] = $new_pword;
		$user['email'] = $_POST['email'];
		$user['mail_msg'] = $mail_msg;
		$user['language'] = $_POST['language'];

		//write user configuration
		$fp = fopen('conf/user.json', 'w');
		fwrite($fp, json_encode($user));
		fclose($fp);
		
		$l = getLanguage();
		$info = '<p class="alert alert-success">'.$l['settings_successful_saved'].$l['reload_page_to_view_result'].'</p>';
}
?>
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">
			<?=$l['admin_area']?>
			<small><?=$l['user_data']?></small>
		</h1>
	</div>
</div>
<div class="raw">
	<div class="col-md-12">
		<?php 
			if($user['username'] == 'admin'){
				echo '<p class="alert alert-warning">'.$l['std_user_warning'].'</p>';
			}
			if ($user['password'] == md5('123456')){
				echo '<p class="alert alert-danger">'.$l['std_pwd_danger'].'</p>';
			}
		?>
		<?php if($_POST) {echo $info;} ?>
		<form class="form-horizontal" action="index.php?page=profile" method="post" autocomplete="off">
			<div class="form-group">
				<label for="uname" class="col-sm-4 control-label hh_form"><?=$l['username']?></label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="uname" name="uname" placeholder="<?=$l['username']?>" value="<?php echo $user['username']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="email" class="col-sm-4 control-label hh_form"><?=$l['email']?></label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="uname" name="email" placeholder="<?=$l['email']?>" value="<?php echo $user['email']; ?>">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-8 col-md-offset-4">
					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" name="mail_msg" <?php ($user['mail_msg']) ? print 'checked' : print ''; ?>> <?=$l['at_new_msg_send_email_to_me']?>
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="language" class="col-sm-4 control-label hh_form"><?=$l['language']?> <?=$l['admin_area']?></label>
				<div class="col-sm-8">
					<select class="form-control" id="language" name="language">
						<?php 
							$lang_file = getLanguageFiles();
							foreach($lang_file as $key => $value){
						?>
							<option value="<?=$value?>" <?=($user['language']==$value)? 'selected': ''?>><?=$value?></option>
							<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="pword" class="col-sm-4 control-label hh_form"><?=$l['password']?></label>
				<div class="col-sm-8">
					<input type="password" name="password" value="" style="display: none" />
					<input type="password" class="form-control" id="pword" name="pword" placeholder="<?=$l['password']?>" autocomplete="off">
				</div>
			</div>
			<div class="form-group">
				<label for="pwordv" class="col-sm-4 control-label hh_form"><?=$l['password']?> (<?=$l['repeate']?>)</label>
				<div class="col-sm-8">
					<input type="password" class="form-control" id="pwordv" name="pwordv" placeholder="<?=$l['password']?>" value="">
					<p><?=$l['only_needed_to_change_password']?></p>
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
