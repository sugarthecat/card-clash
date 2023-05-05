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


$out = "{";
$username = $_GET["un"];
$password = $_GET["pw"];
$signedIn = true;
if ($username != preg_replace("/[^a-zA-Z0-9]+/", "", $username)) {
    $signedIn = false;
}
if ($password != preg_replace("/[^a-zA-Z0-9]+/", "", $password)) {
    $signedIn = false;
}
$sql = "SELECT * FROM user WHERE LOWER(username) = LOWER(\"" . $username . "\") AND password = \"" . $password . "\"";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $signedIn = false;
}
if ($signedIn) {
    $sql = "SELECT * FROM game_player INNER JOIN user on user.user_id = game_player.user_id WHERE username = \"" . $username . "\"";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        //not in lobby
        $sql = "SELECT * FROM game_player";
        $result = $conn->query($sql);
        if ($result->num_rows == 0) {
            $sql = "INSERT INTO game_player ( user_id, last_turn, last_server_contact, last_turn_int) SELECT user_id, now(), now(), 1 FROM user WHERE username = \"" . $username . "\" LIMIT 1";
        } else {
            $sql = "INSERT INTO game_player ( user_id, last_turn, last_server_contact, last_turn_int) SELECT user_id, now(), now(), 1 + (SELECT last_turn_int FROM game_player ORDER BY last_turn_int desc LIMIT 1) FROM user WHERE username = \"" . $username . "\" LIMIT 1";
        }
        $conn->query($sql);
        $sql = "INSERT INTO game_card (card_id, user_id) SELECT card_id, user_id FROM deck_card INNER JOIN deck ON deck_card.deck_id = deck.deck_id INNER JOIN user on user.selected_deck = deck.deck_id WHERE username = \"" . $username . "\"";
        $conn->query($sql);
        
    } else {
        // already in lobby
        $sql = "UPDATE game_player INNER JOIN user ON user.user_id = game_player.user_id SET last_server_contact = now() WHERE username = \"" . $username . "\"";
        $conn->query($sql);
    }

    $out = $out . "\"cards\": [";
    $sql = "SELECT card_name, card_sprite, health, damage FROM game_card INNER JOIN deck_card ON game_card.card_id = deck_card.card_id INNER JOIN user on user.user_id = game_card.user_id WHERE username = \"" . $username . "\" AND play_status = 1";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $out = $out . "{\"name\":\"" . $row["card_name"] . "\", \"icon\": \"" . $row["card_sprite"] . "\", \"health\": \"" . $row["health"] . "\", \"damage\": \"" . $row["damage"] . "\"}";
    }
    while ($row = $result->fetch_assoc()) {
        $out = $out . ",{\"name\":\"" . $row["card_name"] . "\", \"icon\": \"" . $row["card_sprite"] . "\", \"health\": \"" . $row["health"] . "\", \"damage\": \"" . $row["damage"] . "\"}";
    }
    $out = $out . "],";
}
$sql = "SELECT username, health FROM user INNER JOIN game_player ON game_player.user_id = user.user_id ORDER BY last_turn asc";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $out = $out . "\"players\": [";
    if ($row = $result->fetch_assoc()) {
        $out = $out . "{\"name\":\"" . $row["username"] . "\", \"health\": \"" . $row["health"] . "\"}";
    }
    while ($row = $result->fetch_assoc()) {
        $out = $out . ",{\"name\":\"" . $row["username"] . "\", \"health\": \"" . $row["health"] . "\"}";
    }
    $out = $out . "]";
    // output data of each row
}
$out = $out . "}";
echo $out;
?>