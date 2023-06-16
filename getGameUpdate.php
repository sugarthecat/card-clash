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

$sql = "DELETE FROM game_player WHERE last_server_contact < DATE_SUB(NOW(), INTERVAL '45' SECOND) OR health < 1";
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
$gameActive = true;
$sql = "SELECT is_active FROM game_status";
$result = $conn->query(($sql));
if ($row = $result->fetch_assoc()) {
    if ($row["is_active"] == '1') {
        $out = $out . "\"active\":true";
    } else {
        $gameActive = false;
        $out = $out . "\"active\":false";
    }
}
if ($signedIn) {
    $sql = "SELECT * FROM game_player INNER JOIN user on user.user_id = game_player.user_id WHERE username = \"" . $username . "\"";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        //not in lobby
        if (!$gameActive) {
            //insert player
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

            $sql = "UPDATE game_card SET play_status = 0 WHERE (SELECT user_id FROM user WHERE username = \"" . $username . "\") = user_id ORDER BY RAND ()  LIMIT 7";
            $conn->query($sql);
        }else{
            $signedIn = false;
        }
    } else {
        // already in lobby
        $sql = "UPDATE game_player INNER JOIN user ON user.user_id = game_player.user_id SET last_server_contact = now() WHERE username = \"" . $username . "\"";
        $conn->query($sql);
    }
}
if ($signedIn) {
    $out = $out . ",\"cards\": [";
    $sql = "SELECT card_name, card_sprite, health, damage, game_card.card_id as \"id\", `description` FROM game_card INNER JOIN deck_card ON game_card.card_id = deck_card.card_id INNER JOIN user on user.user_id = game_card.user_id LEFT JOIN special_card_description on game_card.card_id = special_card_description.card_id WHERE username = \"" . $username . "\" AND play_status = 1";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $out = $out . "{\"name\":\"" . $row["card_name"] . "\", \"icon\": \"" . $row["card_sprite"] . "\", \"description\": \"" . $row["description"] . "\", \"health\": \"" . $row["health"] . "\", \"damage\": \"" . $row["damage"] . "\", \"id\": \"" . $row["id"] . "\"}";
    }
    while ($row = $result->fetch_assoc()) {
        $out = $out . ",{\"name\":\"" . $row["card_name"] . "\", \"icon\": \"" . $row["card_sprite"] . "\", \"description\": \"" . $row["description"] . "\", \"health\": \"" . $row["health"] . "\", \"damage\": \"" . $row["damage"] . "\", \"id\": \"" . $row["id"] . "\"}";
    }
    $out = $out . "]";
}else{
    $out = $out. ",\"cards\": [] ";
}
$sql = "SELECT username, health, user.user_id as \"id\", folder FROM user INNER JOIN game_player ON game_player.user_id = user.user_id INNER JOIN deck ON user.selected_deck = deck.deck_id ORDER BY last_turn asc";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $out = $out . ",\"players\": [";
    if ($row = $result->fetch_assoc()) {
        $out = $out . "{\"name\":\"" . $row["username"] . "\", \"health\": \"" . $row["health"] . "\", \"id\": \"" . $row["id"] . "\", \"folder\": \"" . $row["folder"] . "\"}";
    }
    while ($row = $result->fetch_assoc()) {
        $out = $out . ",{\"name\":\"" . $row["username"] . "\", \"health\": \"" . $row["health"] . "\", \"id\": \"" . $row["id"] . "\", \"folder\": \"" . $row["folder"] . "\"}";
    }
    $out = $out . "]";
    // output data of each row
}else{
    $sql = "UPDATE game_status SET is_active = 0";
    $conn->query($sql);
}
$sql = "SELECT log_msg, log_icon FROM activity_log ORDER BY inc desc LIMIT 20";
$result = $conn->query($sql);
$out = $out . ",\"logs\": [";
if ($result->num_rows > 0) {
    if ($row = $result->fetch_assoc()) {
        $out = $out . "{\"msg\":\"" . $row["log_msg"] . "\",\"img\":\"" . $row["log_icon"] . "\"}";
    }
    while ($row = $result->fetch_assoc()) {
        $out = $out . ",{\"msg\":\"" . $row["log_msg"] . "\",\"img\":\"" . $row["log_icon"] . "\"}";
    }
    // output data of each row
}
$out = $out . "]";
$out = $out . "}";
echo $out;
?>