<?php
# diser Code muss an erster stelle der Datei wo das GB erscheinen soll
session_start();
include('hhgb/functions.php');
$sgs = getGBsettings();
/* man kann eine bestimmte Sprache einem GB aufzwingen 
* in dem man folgende funktion verwendet:
* setLanguage('de');
*/
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
<!--debug-->
<div style="display:none"><?php  print_r($sgs); ?></div>
<!-- Dieser Teil kommt in den Textbereich der Seite oft auch Content-Bereich genannt. ANFANG GB Body. Von hier -->
	<h3 class="hh_form"><?php echo $sgs['title'];?></h3>
	<!-- Load the submit form -->
	<?php showGBookForm(basename(__FILE__)) ?>
	
	<!-- Load all Posts from database -->
	<?php showGBookPosts() ?>

	<!-- Load page navigation for GB -->
	<?php showGBpageNavi(); ?>
		
	<div class="raw">
		<p><center><a href="hhgb/">Administration</a></center></p>
	</div>
<!-- bis hier. ENDE GB Body -->
</body>

</html> 