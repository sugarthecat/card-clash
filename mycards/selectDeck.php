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
$deck = $_GET["deck"];
if ($username != preg_replace("/[^a-zA-Z0-9]+/", "", $username)) {
    die("{\"error\": \"Username must consist only of letters and numbers\"}");
}
if ($password != preg_replace("/[^a-zA-Z0-9]+/", "", $password)) {
    die("{\"error\": \"password must consist only of letters and numbers\"}");
}
if ($deck != preg_replace("/[^0-9]+/", "", $deck)) {
    die("{\"error\": \"password must consist only of letters and numbers\"}");
}
$sql = "SELECT * FROM game_status WHERE is_active = 0";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $sql = "SELECT * FROM game_player INNER JOIN user WHERE lower(username) = \"" . $username . "\"  AND password = \"" . $password . "\"";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        die("7");
    }
}
$sql = "UPDATE user INNER JOIN deck_ownership ON user.user_id = deck_ownership.user_id SET selected_deck = " . $deck . " WHERE lower(username) = \"" . $username . "\" AND password = \"" . $password . "\"";
$conn->query($sql);
die("1");
?>