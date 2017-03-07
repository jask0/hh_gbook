<?php
include 'conf/lang/de.php';
$set_dbc = 1;
$config_file = 'conf/config.php';

if(!isset($dbc)){
    $dbc = array(
                'servername' => 'localhost',
                'username' => '',
                'password' => '',
                'dbname' => '',
                'table' => '',
                'installed' => 'no'
            );
}

function testConn($db_test){
	if($db_test['username'] != "" and $db_test['dbname'] != ""){
		$connection = @mysqli_connect($db_test['servername'], $db_test['username'], $db_test['password'], $db_test['dbname']);
		// Check connection
		if (!$connection) {
			return 0;
		}else {
			return 1;
		}
	}
	return 0;
}

if(isset($_POST['install']) && $_POST['install']=="dbc" && $_GET['dbc']==1){
    global $dbc;
	global $config_file;
	
	//change user configuration
	$dbc['servername'] = $_POST['servername'];
	$dbc['username'] = $_POST['username'];
	$dbc['password'] = $_POST['password'];
	$dbc['dbname'] = $_POST['dbname'];
	$dbc['table'] = $_POST['table'];
	
	
	if(testConn($dbc)){
		//write user configuration
		
		$content = "<?php\n"
					. "\$dbc = array(\n"
					. "'servername' => '$dbc[servername]',\n"
					. "'username' => '$dbc[username]',\n"
					. "'password' => '$dbc[password]',\n"
					. "'dbname' => '$dbc[dbname]',\n"
					. "'table' => '$dbc[table]',\n"
					. "'installed' => 'yes');\n";
		
		file_put_contents($config_file, $content);
		$info = "<p class=\"alert alert-success\">$l[dbc_data_successful_saved]</p>";
		$dbc['installed'] = "yes";
		#include 'conf/config.php';
		
		
		
	} else {
		$info = "<p class=\"alert alert-danger\">$l[dbc_data_not_saved]</p>";
	}
}

if (isset($_GET['dbc']) && $_GET['dbc'] == "1" || isset($dbc) && $dbc['dbname'] !='' && $dbc['installed'] =='yes') {
    $set_dbc = 0;
    include 'conf/connect.php';
}

if(isset($_POST['install']) && $_POST['install']=="db" && $_GET['db']==1){
	global $dbc;
	
	$set_dbc = 0;
	include 'conf/connect.php';
	
	$conn = $db->getConnection();
	
	// sql to create post table
	$q = "CREATE TABLE ".$dbc['table']."posts (
	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	datum TIMESTAMP,
	name VARCHAR(50) NOT NULL,
	email VARCHAR(100),
	homepage VARCHAR(100),
	betreff VARCHAR(100),
	bild_url VARCHAR(100),
	nachricht TEXT NOT NULL,
	kommentar TEXT,
	public TINYINT(1),
	gb TINYINT(1)
	) CHARACTER SET utf8 COLLATE utf8_unicode_ci;";

	if (mysqli_query($conn, $q)) {
		$info = "<p class=\"alert alert-success\">".$dbc['table']."post =>$l[tabel_successful_created]</p>";
	} else {
		$info= "<p class=\"alert alert-danger\">$l[error_table_not_created]: " . mysqli_error($conn) . "</p>";
	}
		// sql to create config table
	$q = "CREATE TABLE ".$dbc['table']."config (
	id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	variable VARCHAR(100) NOT NULL UNIQUE,
	value TEXT,
	meta TINYINT
	) CHARACTER SET utf8 COLLATE utf8_unicode_ci;";

	if (mysqli_query($conn, $q)) {
		$info .= "<p class=\"alert alert-success\">".$dbc['table']."config =>$l[tabel_successful_created]!</p>"
					   . "<p class=\"alert alert-info\">Login Info:<br>&ensp;&ensp;&ensp;&bull;&ensp;Username: admin<br>&ensp;&ensp;&ensp;&bull;&ensp;Password: 123456</p>";
	} else {
		$info .= "<p class=\"alert alert-danger\">$l[error_table_not_created]: " . mysqli_error($conn) . "</p>";
	}
		
	// sql to import first config data
	$salt = time();
	$default_pw = sha1("123456"+$salt+"#+abhGD.!aseivtrn457AGFDH");
	$qd = array();
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('user', 'admin', '1');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('email', 'email@email.de', '1');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('password', '".$default_pw."', '1');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('mail_msg', '0', '1');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('user_language', 'de', '1');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('salt', '".$salt."', '0');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('captcha_on', '1', '0');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('gb_title', 'MyGB', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('info', 'My Info for you!', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('note', 'My Info for you!', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('gb_language', 'de', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('view_email_input', '1', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('view_hp_input', '1', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('view_img_url_input', '1', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('view_subject_input', '1', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('posts_on_page', '5', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('scale_image', '1', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('post_instant_online', '0', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('send_btn_color', 'btn-success', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('link_btn_color', 'btn-default', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('highlite_field_color', 'has-success', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('note_text_color', 'text-success', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('use_custom_css', '0', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('custom_css', '{\".custom-panel\":{\"border-color\":\"A7093E\"},\".custom-heading\":{\"background-color\":\"650213\",\"color\":\"FFB027\"},\".custom-body\":{\"background-color\":\"840A12\",\"color\":\"EAD72D\"},\".custom-footer\":{\"background-color\":\"81060A\",\"color\":\"B1A714\"},\".custom-thumbnail\":{\"background-color\":\"EC2438\",\"border-color\":\"EC2438\"}}', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('smilies_on', '1', '2');");
	array_push($qd, "INSERT INTO ".$dbc['table']."config (`variable`, `value`, `meta`) VALUES ('smilies', '{\"list\":[\"icon_(1).gif\",\"icon_(10).gif\",\"icon_(11).gif\",\"icon_(12).gif\",\"icon_(13).gif\" ,\"icon_(14).gif\",\"icon_(15).gif\",\"icon_(16).gif\",\"icon_(17).gif\",\"icon_(18).gif\" ,\"icon_(19).gif\",\"icon_(2).gif\",\"icon_(20).gif\",\"icon_(21).gif\",\"icon_(22).gif\" ,\"icon_(23).gif\",\"icon_(24).gif\",\"icon_(3).gif\",\"icon_(4).gif\",\"icon_(5).gif\", \"icon_(6).gif\",\"icon_(7).gif\",\"icon_(8).gif\",\"icon_(9).gif\"], \"folder\":\"yellow\",\"ext\":\"gif\"}', '2');");

	foreach ($qd as $key => $query) {
		if (mysqli_query($conn, $query)) {
			$info .= "<p class=\"alert alert-success config-info\">".$query."</p>";
		} else {
			$info .= "<p class=\"alert alert-danger\">$l[error_table_not_created]: " . mysqli_error($conn) . "</p>";
		}
	}
	 
}
?>
<!DOCTYPE html>
<html>
<head>
<title><?=$l['install']?></title>
<!-- Font-Awesome CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<!-- Extern Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<!-- Bootstrap JavaScript -->
<script src="conf/js/bootstrap.min.js"></script>
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
									<b>Table Name Prefix:</b>
									<input type="text" name="table" value="hhgb_" class="form-control"><br>
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
