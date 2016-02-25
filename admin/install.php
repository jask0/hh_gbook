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
nachricht TEXT NOT NULL,
kommentar TEXT,
public TINYINT(1),
gb TINYINT(1)
);";

if (mysqli_query($conn, $q1)) {
    echo "Tabel 'hh_gbook' successful created!<br>";
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
posts INT(3) DEFAULT 5,
public TINYINT(1) DEFAULT 0,
msg VARCHAR(400),
error VARCHAR(400),
);";

if (mysqli_query($conn, $q2)) {
    echo "Tabel 'hh_gbsettings' successful created!<br>";
} else {
    echo "FEHLER beim erstellen der Tabelle: " . mysqli_error($conn);
}

$q3 = "INSERT INTO hh_gbsettings (title,email,homepage,image,subject,posts,public,msg,error) VALUES ('MyGB', 1, 1, 1, 1, 5, 0, 'Red fields required','Error: Message don´t saved!')";
if (mysqli_query($conn, $q3)) {
    echo "Guestbook 1 successful created!<br>";
} else {
    echo "FEHLER beim erstellen der Tabelle: " . mysqli_error($conn);
}

mysqli_close($conn);
?>