<?php
include('functions.php');
$set_dbc = 0;

if(isset($_POST['install']) && $_POST['install']=="dbc" && $_GET['dbc']==1){
	//change user configuration
	$db['servername'] = $_POST['servername'];
	$db['username'] = $_POST['username'];
	$db['password'] = $_POST['password'];
	$db['dbname'] = $_POST['dbname'];
	$db['table'] = $_POST['table'];
	$db['installed'] = "yes";

	//write user configuration
	$fp = fopen('conf/dbc.json', 'w');
	fwrite($fp, json_encode($db));
	fclose($fp);
	$info = "<p class=\"alert alert-success\">$l[dbc_data_successful_saved]</p>";
}

if ($db['installed'] == "no") {
    $set_dbc = 1;
}

if(isset($_POST['install']) && $_POST['install']=="db" && $_GET['db']==1){
	// sql to create post table
	$q = "CREATE TABLE ".$db['table']." (
	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	datum TIMESTAMP,
	name VARCHAR(30) NOT NULL,
	email VARCHAR(100),
	homepage VARCHAR(100),
	betreff VARCHAR(100),
	bild_url VARCHAR(100),
	nachricht TEXT NOT NULL,
	kommentar TEXT,
	public TINYINT(1),
	gb TINYINT(1)
	);";

	if (mysqli_query($conn, $q)) {
		$info = "<p class=\"alert alert-success\">".$db['table']." =>$l[tabel_successful_created]</p><p>Login Info:</p><ul><li>Username: admin</li><li>Password: 123456</li></p>";
	} else {
		$info= "<p class=\"alert alert-danger\">$l[error_table_not_created]: " . mysqli_error($conn) . "</p>>";
	}
	 
}
?>
<!DOCTYPE html>
<html>
<head>
<title><?=$l['install']?></title>
<?php loadMeta(); ?>
<!-- Extern Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

</head>
<body>
<br><br><br><br>
   <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?=$l['gb']?> <?=$l['install']?></h3>
                    </div>
                    <div class="panel-body">
						<?php
							if($set_dbc){
								if(isset($_POST['install']) && $_POST['install']=="dbc"){ echo $info;}
						?>
							<form role="form" action="install.php?dbc=1" method="post" >
								<fieldset>
									<!-- Change this to a button or input when using this as a form -->
									<b>Servername:</b>
									<input type="text" name="servername" value="localhost" class="form-control"><br>
									<b>Username:</b>
									<input type="text" name="username" placeholder="root" class="form-control"><br>
									<b>Password:</b>
									<input type="password" name="password" class="form-control"><br>
									<b>Database Name:</b>
									<input type="text" name="dbname" class="form-control"><br>
									<br>
									<b>Table Name:</b>
									<input type="text" name="table" value="hh_gbook" class="form-control"><br>
									<br>
									<input type="hidden" name="install" value="dbc" >
									<button type="submit" name="submit" class="btn btn-success"><?=$l['save']?></button>
								</fieldset>
							</form>
						
						<?php } else {
							if(isset($_POST['install']) && $_POST['install']=="db"){ echo $info;} ?>
							<form role="form" action="install.php?db=1" method="post" >
								<fieldset>
									<!-- Change this to a button or input when using this as a form -->
									<?php if(!isset($_GET['db'])){ ?>
									
									<button type="submit" name="submit" class="btn btn-lg btn-success btn-block"><?=$l['install']?></button>
									<input type="hidden" name="install" value="db" >
									<?php } else { ?>
									<a href="../" class="btn btn-lg btn-info btn-block"><?=$l['to_gb']?></a>
									<?php } ?>
								</fieldset>
							</form>
						<?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
