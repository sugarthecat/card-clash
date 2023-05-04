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

$sql = "DELETE FROM game_player WHERE last_server_contact < DATE_SUB(NOW(), INTERVAL '45' SECOND)";
$result = $conn->query($sql);


$username = $_GET["un"];
$password = $_GET["pw"];
if ($username != preg_replace("/[^a-zA-Z0-9]+/", "", $username)) {
    die("{\"error\": \"Username must consist only of letters and numbers\"}");
}
if ($password != preg_replace("/[^a-zA-Z0-9]+/", "", $password)) {
    die("{\"error\": \"password must consist only of letters and numbers\"}");
}

$sql = "SELECT * FROM user INNER JOIN game_player WHERE LOWER(username) = LOWER(\"" . $username . "\") AND password = \"" . $password . "\"";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("{\"error\": \"Invalid login\"}");
}
$sql = "SELECT * FROM game_player INNER JOIN user on user.user_id = game_player.user_id WHERE username = \"" . $username . "\" AND last_turn = (SELECT last_turn FROM game_player ORDER BY last_turn_int asc LIMIT 1)";

$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Failed: NOt oldest turn");
}
$sql = "UPDATE game_player INNER JOIN user ON user.user_id = game_player.user_id SET last_turn = now() WHERE username = \"" . $username . "\"";
$conn->query($sql);
$sql = "UPDATE game_player INNER JOIN user ON user.user_id = game_player.user_id SET last_turn_int = 1+(SELECT last_turn_int FROM game_player ORDER BY last_turn_int desc LIMIT 1) WHERE username = \"" . $username . "\"";
$conn->query($sql);
$out = "executed turn";
echo $out;
?>