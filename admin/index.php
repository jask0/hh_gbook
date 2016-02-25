<?php
session_start();
if(!isset($_SESSION['username'])){
		header('Location: login.php');
}
include('conf/load_settings.php');
$sgs = getGBsettings($conn);
if($_POST && $_POST['beitraege_pro_seite'] >0){
	$update_settings = 1;
	$new_pword = $sgs['password'];
	if($_POST['pword'] != ''){
		if($_POST['pword'] == $_POST['pwordv']){
			$new_pword = md5($_POST['pwordv']);
		}else {
			$update_settings = 0;
			$info = '<p class="alert alert-danger">Passwort Fehler! Einstellungen nicht gespeichert!</p>';
		}
	} 
if($update_settings){
	$q = "UPDATE hh_gbsettings SET title='".$_POST['gb_title']."', email='".$_POST['show_email'];
	$q .="', homepage='".$_POST['show_homepage']."', image='".$_POST['show_image']."', subject='".$_POST['show_subject'];
	$q .="', posts='".$_POST['beitraege_pro_seite']."', public='".$_POST['public']."', msg='".$_POST['msg']."',";
	$q .=" error='".$_POST['fehler']."', username='".$_POST['uname']."', password='".$new_pword."' WHERE id = 1;";
	
	$r = mysqli_query($conn, $q);
	$info = '<p class="alert alert-success">Erfolgreich gespeichert</p>';
}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>G&auml;stebuch: HomepageHelfer</title>
	<?php loadMeta(); ?>
	<!-- Bootstrap Core CSS -->
	<link href="conf/css/bootstrap.min.css" rel="stylesheet">

	<!-- Custom CSS -->
	<link href="conf/css/sb-admin.css" rel="stylesheet">
	<style>
		.top-nav > li > a.new_msg {
			color: red;
		}
		
		.top-nav > li > a.new_msg:hover {
			color:white;
		}
	</style>
</head>

<body>
<div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="../"><?php echo $sgs['title']; ?></a>
            </div>
            <!-- Top Menu Items -->
            <ul class="nav navbar-right top-nav">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-envelope"></i> <b class="caret"></b></a>
                    <ul class="dropdown-menu message-dropdown">
						<li class="message-footer">
                            <a href="#"><strong>3 letzte Nachrichten</strong></a>
                        </li>
					<?php
						$abfrage = "SELECT * FROM hh_gbook ORDER BY id DESC LIMIT 3";
						$abfrage_antwort = mysqli_query($conn, $abfrage);
	
						if ( ! $abfrage_antwort )
						{
						  die('Abfrage Fehler: ' . mysqli_error($conn));
						} 
						
						while ($zeile = mysqli_fetch_array( $abfrage_antwort, MYSQL_ASSOC))
						{?>
							<li class="message-preview">
								<a href="?page=edit&id=<?php echo $zeile['id']; ?>" title="Die letzten 3 Nachrichten">
									<div class="media">
										<?php if($zeile['bild_url'] != '') {; ?>
											<span class="pull-left">
												<img class="media-object" style="width:64px;height:64px;" src="<?php echo $zeile['bild_url']; ?>" alt="">
											</span>
										<?php } ?>
										<div class="media-body">
											<h5 class="media-heading">
												<strong><?php echo $zeile['name']; ?></strong>
											</h5>
											<p class="small text-muted"><i class="fa fa-clock-o"></i> <?php echo $zeile['datum']; ?></p>
											<p><?php echo $zeile['nachricht']; ?></p>
										</div>
									</div>
								</a>
							</li>
					<?php	}
						mysqli_free_result( $abfrage_antwort );
					?>
                        
                       
                        <li class="message-footer">
                            <a href="../">Alle Nachrichten lesen</a>
                        </li>
                    </ul>
                </li>
				<?php 
					$abfrage = "SELECT count(public) FROM hh_gbook WHERE public = 0 ORDER BY id DESC";
					$abfrage_antwort = mysqli_query($conn, $abfrage);
					$count = mysqli_fetch_row($abfrage_antwort);
					if((int)$count[0] > 0){
						$new_msg=1;
					} else{
						$new_msg=0;
					}
					mysqli_free_result( $abfrage_antwort );
				?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle <?php $new_msg ? print 'new_msg': print '';?>" data-toggle="dropdown" title="<?php echo $count[0];?> unveröffentlichte Nachrichten"><i class="fa fa-bell" ></i> <b class="caret"></b></a>
                    <ul class="dropdown-menu message-dropdown">
                       <li class="message-footer">
                            <a href="#"><strong>Unveröffentlichte Nachrichten</strong></a>
                        </li>
					<?php
						$abfrage = "SELECT * FROM hh_gbook WHERE public = 0 ORDER BY id DESC";
						$abfrage_antwort = mysqli_query($conn, $abfrage);
						if ( ! $abfrage_antwort )
						{
						  die('Abfrage Fehler: ' . mysqli_error($conn));
						} 
						
						while ($zeile = mysqli_fetch_array( $abfrage_antwort, MYSQL_ASSOC))
						{?>
							<li class="message-preview">
								<a href="?page=edit&id=<?php echo $zeile['id']; ?>">
									<div class="media">
										<?php if($zeile['bild_url'] != '') {; ?>
											<span class="pull-left">
												<img class="media-object" style="width:64px;height:64px;" src="<?php echo $zeile['bild_url']; ?>" alt="">
											</span>
										<?php } ?>
										<div class="media-body">
											<h5 class="media-heading">
												<strong><?php echo $zeile['name']; ?></strong>
											</h5>
											<p class="small text-muted"><i class="fa fa-clock-o"></i> <?php echo $zeile['datum']; ?></p>
											<p><?php echo htmlentities($zeile['nachricht']); ?></p>
										</div>
									</div>
								</a>
							</li>
						<?php	}
							mysqli_free_result( $abfrage_antwort );
						?>
                        <li class="divider message-footer"></li>
                        <li>
                            <a href="?page=offline&id=0">Alle sehen</a>
                        </li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $sgs['username']; ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="index.php"><i class="fa fa-fw fa-user"></i> Startseite</a>
                        </li>
                        <li>
                            <a href="?page=offline&id=0"><i class="fa fa-fw fa-envelope"></i> <?php echo $count[0]; ?> neue Posts</a>
                        </li>
                        <li>
                            <a href="index.php"><i class="fa fa-fw fa-gear"></i> Einstellungen</a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="login.php?logout=1"><i class="fa fa-fw fa-power-off"></i> Ausloggen</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
                <ul class="nav navbar-nav side-nav">
					<li>
                        <a href="../"><i class="fa fa-fw fa-desktop"></i> Zum G&auml;stebuch</a>
                    </li>
                    <li>
                        <a href="index.php"><i class="fa fa-fw fa-wrench"></i> Einstellungen</a>
                    </li><!--
                    <li>
                        <a href="#"><i class="fa fa-fw fa-edit"></i> Neues GB anlegen</a>
                    </li>
                    <li>
                    <li class="active">
                        <a href="#"><i class="fa fa-fw fa-file"></i> Bearbeiten</a>
                    </li>-->
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </nav>

        <div id="page-wrapper">

            <div class="container-fluid">
			<?php
			if(isset($_GET['page'])){
				include('conf/view_'.$_GET['page'].'.php');
			} else {
				include('conf/view_index.php');
			}
			?>
	
			</div>
            <!-- /.container-fluid -->

        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    <!-- jQuery -->
    <script src="conf/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="conf/js/bootstrap.min.js"></script>
</body>

</html>
