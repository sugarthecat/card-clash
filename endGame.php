<?php


$servername = "localhost";
$username = "root";
$password = "Keefe2012";
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
    $signedIn = false;
}
if ($password != preg_replace("/[^a-zA-Z0-9]+/", "", $password)) {
    $signedIn = false;
}
$sql = "SELECT * FROM user INNER JOIN game_player ON user.user_id = game_player.user_id WHERE LOWER(username) = LOWER(\"" . $username . "\") AND password = \"" . $password . "\"";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $signedIn = false;
}
if($signedIn){
    $sql = "UPDATE game_status SET is_active = 0";
    $conn->query($sql);
    $sql = "DELETE FROM game_player";
    $conn->query($sql);
    $sql = "DELETE FROM activity_log";
    $conn->query($sql);
}
?>