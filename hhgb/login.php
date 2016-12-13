<?php
session_start();
require 'conf/config.php';
if (!isset($dbc)) {
    header('Location: install.php');
}
require 'gb.php';
$gb = new GB;
$l = $gb->getLanguage();

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
	if (preg_match('%^[A-Za-z\.\' \-]{2,15}$%', stripslashes(trim($_POST['username'])))) {
	$un =  escape_data($_POST['username'], $gb->getConn());
	} else {
		$un = FALSE;
		$info = '<p class="alert alert-danger"><font size="+1">Der eingegebene Benutzername oder das Passwort sind ungültig!</font><br>';
	}
	if (preg_match ('%^[A-Za-z0-9]{6,20}$%', stripslashes(trim($_POST['password'])))) {
	$pw =  escape_data($_POST['password'], $gb->getConn());
	$p = sha1($pw+$gb->getSalt());
    } else {
		$p = FALSE;
		$info = '<p class="alert alert-danger"><font size="+1">Der eingegebene Benutzername oder das Passwort sind ungültig!</font><br>';
	}
	
	$user = $gb->getUserSettings();
	if(($un == $user['user']) and ($p == $user['password'])){
		$_SESSION['username'] = $un;
		if(isset($_POST['cookie'])){
			setcookie("username",session_id(),time() + (86400*10), "/"); // = 10 Days
		}
		$info = '<p class="alert alert-success">'.$l['login_successful'].'<br><b><a href="../">'.$l['to_gb'].'</a><b></p>';
		$info .= '<center><a class="btn btn-success" href="index.php">'.$l['admin_area'].'</a>&nbsp;&nbsp;<a class="btn btn-danger" href="login.php?logout=1">'.$l['logout'].'</a></center>';
	} else {
		$info .= $l['login_failed'].'<br><b>'.$l['please_try_again'].'</b></p>';
	}
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<!-- Font-Awesome CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
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
