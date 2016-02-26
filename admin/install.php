<?php
include('conf/load_settings.php');

if($_POST){
	// sql to create post table
	$q1 = "CREATE TABLE hh_gbook (
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

	if (mysqli_query($conn, $q1)) {
		$info = "<p class=\"alert alert-success\">Tabel 'hh_gbook' successful created!<br>";
	} else {
		$info= "<p class=\"alert alert-danger\">FEHLER beim erstellen der Tabelle: " . mysqli_error($conn) . "<br>";
	}
	 
	// sql to create settings table
	$q2 = "CREATE TABLE hh_gbsettings (
	id INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(100),
	email TINYINT(1),
	homepage TINYINT(1),
	image TINYINT(1),
	subject TINYINT(1),
	posts INT(3) DEFAULT 5,
	public TINYINT(1) DEFAULT 0,
	msg VARCHAR(400),
	error VARCHAR(400)
	);";

	if (mysqli_query($conn, $q2)) {
		$info .= "Tabel 'hh_gbsettings' successful created!<br>";
	} else {
		$info .= "FEHLER beim erstellen der Tabelle: " . mysqli_error($conn) . "<br>";
	}

	$q3 = "INSERT INTO hh_gbsettings (title,email,homepage,image,subject,posts,public,msg,error) VALUES ('MyGB', 1, 1, 1, 1, 5, 0, 'Red fields required','Error: Message don´t saved!')";
	if (mysqli_query($conn, $q3)) {
		$info .= "Guestbook 1 successful created!<br></p>";
	} else {
		$info = "FEHLER beim erstellen der Tabelle: " . mysqli_error($conn) . "</p>";
	}

	mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<?php loadMeta(); ?>
</head>
<body>
<br><br><br><br>
   <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">GB installieren</h3>
                    </div>
                    <div class="panel-body">
						<?php if($_POST){ echo $info;} ?>
                        <form role="form" action="install.php" method="post" >
                            <fieldset>
                                <!-- Change this to a button or input when using this as a form -->
								<?php if(!$_POST){ ?>
								
								<button type="submit" name="submit" class="btn btn-lg btn-success btn-block">Installieren</button>
								<?php } else { ?>
								<a href="../" class="btn btn-lg btn-info btn-block">Zur&uuml;ck zum GB</a>
								<?php } ?>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>