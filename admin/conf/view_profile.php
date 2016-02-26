<?php
	if(!isset($_SESSION['username'])){
		die('Direkter Zugang nicht erlaubt! Bitte einloggen!');
	}
	include 'conf/user.php';
	if($_POST){
		$new_pword = $user['password'];
		if($_POST['pword'] != ''){
			if($_POST['pword'] == $_POST['pwordv']){
				$new_pword = md5($_POST['pwordv']);
			}else {
				$info = '<p class="alert alert-danger">Passwort Fehler! Einstellungen nicht gespeichert!</p>';
			}
		}
		if(isset($_POST['mail_msg'])){
			$mail_msg = 1;
		} else {
			$mail_msg = 0;
		}
		
		$fp = fopen('conf/user.php', 'w') or die($ERROR_CREATING_FILE);
		$write = '<?php $user = Array(';
		$write .="'username' => '".$_POST['uname']."',";
		$write .="'password' => '".$new_pword."',";
		$write .="'mail_msg' => ".$mail_msg.",";
		$write .="'email' => '".$_POST['email']."'); ?>";
		fwrite($fp,$write);
		fclose($fp);
		$info = '<p class="alert alert-success">Daten gespeichert!</p>';
		include 'user.php';
}
?>
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">
			Administration
			<small>Nutzerprofil</small>
		</h1>
	</div>
</div>
<div class="raw">
	<div class="col-md-12">
		<?php if($_POST) {echo $info;} ?>
		<form class="form-horizontal" action="index.php?page=profile" method="post" autocomplete="off">
			<div class="form-group">
				<label for="uname" class="col-sm-4 control-label hh_form">Benutzername</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="uname" name="uname" placeholder="Benutzername" value="<?php echo $user['username']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="email" class="col-sm-4 control-label hh_form">E-Mail</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" id="uname" name="email" placeholder="E-Mail" value="<?php echo $user['email']; ?>">
				</div>
			</div>
			<div class="form-group">
				<label for="pword" class="col-sm-4 control-label hh_form">Passwort</label>
				<div class="col-sm-8">
					<input type="password" name="password" value="" style="display: none" />
					<input type="password" class="form-control" id="pword" name="pword" placeholder="Passwort" autocomplete="off">
				</div>
			</div>
			<div class="form-group">
				<label for="pwordv" class="col-sm-4 control-label hh_form">Passwort (wiederholen)</label>
				<div class="col-sm-8">
					<input type="password" class="form-control" id="pwordv" name="pwordv" placeholder="Passwort" value="">
					<p>Nur beim &auml;ndern des Passwortes n&ouml;tig</p>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-8 col-md-offset-2">
					<div class="checkbox">
						<label>
							<input type="checkbox" value="1" name="mail_msg" <?php ($user['mail_msg']) ? print 'checked' : print ''; ?>> Bei neuen Nachrichten eine E-Mail an mich senden
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-8">
					<button type="submit" class="btn btn-success" name="submit">Speichern</button>
				</div>
			</div>
		</form>
	</div>
</div>