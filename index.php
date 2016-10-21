<?php
# diser Code muss an erster stelle der Datei wo das GB erscheinen soll
session_start();
require_once('hhgb/gb.php');
$gb = new GB;
/* man kann eine bestimmte Sprache einem GB aufzwingen 
* in dem man folgende funktion verwendet:
* $gb->setLanguage('de');
*/
$gb->setLanguage('ba');
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>G&auml;stebuch: HomepageHelfer</title>

<!-- Zum richtigen darstellen des GB kommt dieser Teil zwischen <head> und </head>. ANFANG GB Meta. Von hier -->
<?php $gb->includeMeta(); ?>
<!-- bis hier. ENDE GB Meta -->
</head>

<body>
    <div style="width:97%;margin:auto;">
        <!-- Dieser Teil kommt in den Textbereich der Seite oft auch Content-Bereich genannt. ANFANG GB Body. Von hier -->
        <h3 class="hh_form"><?php echo $gb->getGbSettings()['gb_title'];?></h3>
        <!-- Load the submit form -->
        <?php $gb->showGBookForm(basename(__FILE__)) ?>

        <!-- Load all Posts from database -->
        <?php $gb->showGBookPosts() ?>

        <!-- Load page navigation for GB -->
        <?php $gb->showGBpageNavi(); ?>

        <div class="raw">
                <p><center><a href="hhgb/">Administration</a></center></p>
        </div>
    <!-- bis hier. ENDE GB Body -->
    </div>
</body>

</html> 
