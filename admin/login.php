<?php
session_start();
include('conf/load_settings.php');
$l = getLanguage();

$sgs = getGBsettings($conn);
$info = '<p class="alert alert-success">'.$l['you_are_alredy_loged_in'].'</p><br>';
$info .= '<center><a class="btn btn-success" href="index.php">'.$l['admin_area'].'</a>  <a class="btn btn-danger" href="login.php?logout=1">'.$l['logout'].'</a></center>';
if(isset($_GET['logout']) && $_GET['logout'] == 1) {
	if(isset($_COOKIE[session_name()])):
		unset($_COOKIE[session_name()]);
        setcookie(session_name(), null, -1, '/');
    endif;
	
	if(isset($_COOKIE['username'])){
		unset($_COOKIE['username']);
		setcookie('username', null, -1, '/');
	}
	header('Location: login.php');
}
if($_POST){
	$username = $_POST['username'];
	$passwort = md5($_POST['password']);
	if(($username == $user['username']) and ($passwort == $user['password'])){
		$_SESSION['username'] = $username;
		if(isset($_POST['cookie'])){
			setcookie("username",session_id(),time() + (86400*10), "/"); // = 10 Days
		}
		$info = '<p class="alert alert-success">'.$l['login_successful'].'<br><b><a href="../">'.$l['to_gb'].'</a><b></p>';
		$info .= '<center><a class="btn btn-success" href="index.php">'.$l['admin_area'].'</a>&nbsp;&nbsp;<a class="btn btn-danger" href="login.php?logout=1">'.$l['logout'].'</a></center>';
	} else {
		$info = '<p class="alert alert-danger">'.$l['login_failed'].'<br><b>'.$l['please_try_again'].'</b></p>';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<?php loadMeta(); ?>
<!-- Extern Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

</head>
<body>
<br><br><br><br>
   <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=$l['login_form']?></h3>
                    </div>
                    <div class="panel-body">
						<?php if (!isset($_SESSION['username'])) { 
							if($_POST){
								echo $info;
							}
						?>
                        <form role="form" action="login.php" method="post" >
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="<?=$l['username']?>" name="username" type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="<?=$l['password']?>" name="password" type="password">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="cookie" type="checkbox" value="1"> <?=$l['remember_me']?>
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
								<button type="submit" class="btn btn-lg btn-success btn-block"><?=$l['login']?></button>
								<a href="../" type="submit" class="btn btn-lg btn-info btn-block"><?=$l['to_gb']?></a>
                            </fieldset>
                        </form>
						<?php } else {
							echo $info;
						} ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>