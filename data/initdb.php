<?php
session_start();
session_destroy();
include '../application/libraries/config.class.php';
include '../application/libraries/database.class.php';
$dbh = new \ebbs\libraries\Database;
$dbh = $dbh->lazyConnect();

$sql = explode(";", file_get_contents('mysql.sql'));
$sql = array_filter($sql);
foreach ($sql as $query) {
    $query = trim($query);
    $dbh->exec($query);
}

echo '<h3>Tables successfully reset!</h3>';

$sql = explode(";", file_get_contents('sampledata.sql'));
$sql = array_filter($sql);


foreach ($sql as $query) {
    $query = trim($query);
    $dbh->exec($query);
}

echo '<h3>Sampledate successfully loaded!</h3>';

header('Location:' . $_SERVER['HTTP_REFERER']);
exit(1);