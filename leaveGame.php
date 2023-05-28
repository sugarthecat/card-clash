<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cardclash";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("{\"error\": \"Connection failed: " . $conn->connect_error . "\"}");
}
$username = $_GET["un"];
$password = $_GET["pw"];
$signedIn = true;
if ($username != preg_replace("/[^a-zA-Z0-9]+/", "", $username)) {
    die();
}
if ($password != preg_replace("/[^a-zA-Z0-9]+/", "", $password)) {
    die();
}
$sql = "DELETE game_player FROM game_player INNER JOIN user ON user.user_id = game_player.user_id WHERE username = \"" . $username . "\" AND password =\"" . $password . "\"";
$conn->query($sql);
die("3")
?>