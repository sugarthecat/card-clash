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
if ($username != preg_replace("/[^a-zA-Z0-9]+/", "", $username)) {
    die();
}
if ($password != preg_replace("/[^a-zA-Z0-9]+/", "", $password)) {
    die();
}
$sql = "SELECT folder FROM user INNER JOIN deck_ownership ON user.user_id = deck_ownership.user_id INNER JOIN deck ON deck.deck_id = deck_ownership.deck_id WHERE selected_deck = deck.deck_id AND LOWER(username) = LOWER(\"" . $username . "\") AND password = \"" . $password . "\"";
$result = $conn->query($sql);
if ($row = $result->fetch_assoc()) {
    die("{\"folder\":\"".$row["folder"]."\"}");
}else{
    die("folder not found");
}
?>