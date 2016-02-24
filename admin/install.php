<?php
include('conf/connect.php');

// sql to create post table
$q1 = "CREATE TABLE hh_gbook (
id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
datum TIMESTAMP,
name VARCHAR(30) NOT NULL,
email VARCHAR(100),
homepage VARCHAR(100),
betreff VARCHAR(100),
bild_url VARCHAR(100),
nachricht TEXT,
kommentar TEXT,
public TINYINT(1),
gb TINYINT(1)
);";

if (mysqli_query($conn, $q1)) {
    echo "Tabelle 'hh_gbook' wurde erfolgreich angelegt!<br>";
} else {
    echo "FEHLER beim erstellen der Tabelle: " . mysqli_error($conn);
}
 
// sql to create settings table
$q2 = "CREATE TABLE hh_gbsettings (
id INT(3) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(100),
email TINYINT(1),
homepage TINYINT(1),
image TINYINT(1),
subject TINYINT(1),
posts INT(3),
msg VARCHAR(400),
error VARCHAR(400),
username VARCHAR(200),
password VARCHAR(200)
);";

if (mysqli_query($conn, $q2)) {
    echo "Tabelle 'hh_gbsettings' wurde erfolgreich angelegt!<br>";
} else {
    echo "FEHLER beim erstellen der Tabelle: " . mysqli_error($conn);
}


mysqli_close($conn);
?>