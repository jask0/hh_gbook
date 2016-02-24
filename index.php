<?php
 # diser Code muss an erster stelle der Datei wo das GB erscheinen soll
 session_start();
 include('admin/conf/load_settings.php');
 $sgs = getGBsettings($conn, 1);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>G&auml;stebuch: HomepageHelfer</title>

<!-- Zum richtigen darstellen des GB kommt dieser Teil zwischen <head> und </head>. ANFANG GB Meta. Von hier -->
<?php loadMeta(); ?>
<!-- bis hier. ENDE GB Meta -->
</head>

<body>
<!-- Dieser Teil kommt in den Textbereich der Seite oft auch Content-Bereich genannt. ANFANG GB Body. Von hier -->
<div class="container">
	<h3 class="hh_form"><?php echo $sgs['title'];?></h3>
	
	<div class="raw">
		<?php showGBookForm(basename(__FILE__)) ?>
	</div> <!-- END .raw -->
	<div class="raw">
		<?php showGBookPosts() ?>
	</div>
	<div class="raw">
		<div class="col-md-12">
		<?php showGBpageNavi(); ?>
		</div>
	</div>
</div> <!-- END .container -->
<p><center><a href="admin/">Administration</a></center></p>
<!-- bis hier. ENDE GB Body -->
</body>

</html> 