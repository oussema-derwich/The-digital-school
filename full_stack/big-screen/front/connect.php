<?php
$mysqli = new mysqli("127.0.0.1", "root", "", "bigscreen");
if ($mysqli->connect_errno) {
    die("Erreur de connexion à la base de données : " . $mysqli->connect_error);
}
?>