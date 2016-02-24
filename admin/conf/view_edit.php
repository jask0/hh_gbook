<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">
			Administration
			<small>Bearbeiten</small>
		</h1>
	</div>
</div>
<?php
	if(!isset($_SESSION['username'])){
		die('Direkter Zugang nicht erlaubt! Bitte einloggen!');
	}
	//include('load_settings.php');
	//$sgs = getGBsettings($conn, 1);
	$id = (int)$_GET['id'];

	if(isset($_POST['submit'])){
		if(isset($_POST['loeschen']) and $_POST['loeschen'] == "1"){
			$abfrage = sprintf("DELETE FROM hh_gbook WHERE id = %d",$id);
			
			if ( mysqli_query($conn, $abfrage) )
			{
				echo '<p class="alert alert-success">Der eintrag wurde erfolgreich gel&ouml;scht!</p>';
			}else{
				die('Ung&uuml;ltige Abfrage: ' . mysqli_error($conn));
			}
		}else{
			$name = $_POST['name'];
			$email = htmlentities($_POST['email']);
			$homepage = htmlentities($_POST['homepage']);
			$betreff = htmlentities($_POST['betreff']);
			$bild_url = htmlentities($_POST['bild_url']);
			$nachricht = str_replace('"','\"',str_replace("'", "\'",$_POST['nachricht']));
			$kommentar = str_replace('"','\"',str_replace("'", "\'",$_POST['kommentar']));
			$public = 0;
			if(isset($_POST['public']) and $_POST['public'] == "1")
				$public=1;
			
			$abfrage = sprintf('UPDATE hh_gbook
								SET name="%s",
									email="%s",
									homepage="%s",
									betreff="%s",
									bild_url="%s",
									nachricht="%s",
									kommentar="%s",
									public=%d
								WHERE id=%d;',$name,$email,$homepage,$betreff,$bild_url,$nachricht,$kommentar,$public,$id);
			
			if ( mysqli_query($conn, $abfrage) )
			{
				echo '<p class="alert alert-success">Der Eintrag wurde erfolgreich bearbeitet!</p>';
			}else{
				die('Ung&uuml;ltige Abfrage: ' . mysqli_error($conn));
			}
		}
	}
	$abfrage = sprintf("SELECT * FROM hh_gbook WHERE id = %s",$id);
	$abfrage_antwort = mysqli_query($conn, $abfrage);
	
	if ( ! $abfrage_antwort )
	{
	  die('Ung&uuml;ltige Abfrage: ' . mysqli_error($conn));
	}
	
	while ($zeile = mysqli_fetch_array( $abfrage_antwort, MYSQL_ASSOC))
	{	$zeile['public']=="1" ? $checked = "checked" : $checked = "";
		$zeile['admin'] = 1;
		showForm($zeile, '?page=edit&id='.$id);
	}
	 
	mysqli_free_result( $abfrage_antwort );
?>