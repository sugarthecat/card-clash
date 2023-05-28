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

$sql = "SELECT is_active FROM game_status";
$result = $conn->query(($sql));
if ($row = $result->fetch_assoc()) {
    if ($row["is_active"] == '0') {
        die("{\"error\":\"Invalid turn, game has not started\"}");
    }
}

$sql = "SELECT * FROM user INNER JOIN game_player WHERE LOWER(username) = LOWER(\"" . $username . "\") AND password = \"" . $password . "\"";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("{\"error\": \"Invalid login\"}");
}
$sql = "SELECT * FROM game_player INNER JOIN user on user.user_id = game_player.user_id WHERE username = \"" . $username . "\" AND last_turn = (SELECT last_turn FROM game_player ORDER BY last_turn_int asc LIMIT 1)";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Failed: Not oldest turn");
}
$validAttack = true;
$target = null;
$cardid = null;
if (array_key_exists("card", $_GET)) {

    $cardid = $_GET["card"];
    if (is_numeric($cardid) != 1) {
        $validAttack = false;
    }
} else {
    $validAttack = false;
}
if (array_key_exists("target", $_GET)) {

    $target = $_GET["target"];
    if (is_numeric($target) != 1) {
        $validAttack = false;
    }
} else {
    $validAttack = false;
}

$sql = "SELECT * FROM game_player INNER JOIN user on user.user_id = game_player.user_id WHERE username = \"" . $username . "\"";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Failed: Not oldest turn");
}

//Attack if possible
// get damage and health from card
$damage = 0;
$health = 0;
$cardname = "";
$sprite = "";
if ($validAttack) {
    $sql = "SELECT damage, health, card_name, card_sprite FROM game_card INNER JOIN user on user.user_id = game_card.user_id INNER JOIN deck_card ON deck_card.card_id = game_card.card_id WHERE game_card.card_id = " . $cardid . " AND LOWER(username) = LOWER(\"" . $username . "\") AND play_status = 1";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $damage = $row["damage"];
        $health = $row["health"];
        $cardname = $row["card_name"];
        $sprite = $row["card_sprite"];
        $sql = "UPDATE game_card INNER JOIN user on user.user_id = game_card.user_id SET play_status = 2 WHERE card_id = " . $cardid . " AND username = \"" . $username . "\"";
        $conn->query($sql);
    } else {
        $validAttack = false;
    }
}
if ($validAttack) {
    $prevHealth = 0;
    $tgtUsername = "";
    $sql = "SELECT health FROM game_player INNER JOIN user on user.user_id = game_player.user_id WHERE username = \"" . $username . "\"";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $prevHealth = $row["health"];
    } else {
        die("Did not find player health");
    }
    $sql = "UPDATE game_player INNER JOIN user on user.user_id = game_player.user_id SET health = " . ($prevHealth + $health) . " WHERE username = \"" . $username . "\"";
    $conn->query(($sql));

    $prevHealth = 0;
    $sql = "SELECT health, username FROM game_player INNER JOIN user ON user.user_id = game_player.user_id WHERE user.user_id = \"" . $target . "\"";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        $prevHealth = $row["health"];
        $tgtUsername = $row["username"];
    } else {
        die("Did not find player health");
    }
    $msg = "";
    if ($health == 0) {
        if ($damage > 0) {
            $msg = $username . " played " . $cardname . ", attacking " . $tgtUsername . " for " . strval($damage) . " hp";
        } else if ($damage == 0) {
            $msg = $username . " played " . $cardname;
        }
    } else {
        if ($damage == 0) {
            $msg = $username . " played " . $cardname . ", healing " . strval($health) . "hp";
        } else {
            $msg = $username . " played " . $cardname . ", healing " . strval($health) . " hp, attacking " . $tgtUsername . " for " . strval($damage) . " hp";
        }
    }
    $sql = "UPDATE game_player SET health = " . ($prevHealth - $damage) . " WHERE user_id = " . $target;
    $result = $conn->query($sql);
    $sql = "INSERT INTO activity_log (log_msg) VALUES (\"" . $msg . "\");";
    $result = $conn->query($sql);
}else{
    
    $sql = "INSERT INTO activity_log (log_msg) VALUES (\"" . $username . " skipped their turn\");";
    $result = $conn->query($sql);
}

// reshuffle old cards
$sql = "SELECT * FROM game_card INNER JOIN user ON user.user_id = game_card.user_id WHERE play_status = 0 AND username = \"" . $username . "\"";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $sql = "SELECT * FROM game_card INNER JOIN user ON user.user_id = game_card.user_id WHERE play_status = 1 AND username = \"" . $username . "\"";
    $result = $conn->query($sql);
    if ($result->num_rows <= 3) {
        $sql = "UPDATE game_card SET play_status = 0 WHERE play_status = 2 AND (SELECT user_id FROM user WHERE username = \"" . $username . "\") = user_id";
        $conn->query($sql);
    }
}
//draw new cards

$sql = "UPDATE game_card SET play_status = 1 WHERE play_status = 0 AND (SELECT user_id FROM user WHERE username = \"" . $username . "\") = user_id ORDER BY RAND () LIMIT 1";
$conn->query($sql);


$sql = "UPDATE game_player INNER JOIN user ON user.user_id = game_player.user_id SET last_turn = now() WHERE username = \"" . $username . "\"";
$conn->query($sql);
$sql = "UPDATE game_player INNER JOIN user ON user.user_id = game_player.user_id SET last_turn_int = 1+(SELECT last_turn_int FROM game_player ORDER BY last_turn_int desc LIMIT 1) WHERE username = \"" . $username . "\"";
$conn->query($sql);
$out = "executed turn";
echo $out;
?>