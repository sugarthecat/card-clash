<?php
//
// FUNCTIONS
//
//
function incrementTurn($user_id, $conn)
{

    $sql = "UPDATE game_player SET last_turn = now() WHERE user_id = \"" . $user_id . "\"";
    $conn->query($sql);
    $sql = "UPDATE game_player SET last_turn_int = 1+(SELECT last_turn_int FROM game_player ORDER BY last_turn_int desc LIMIT 1) WHERE user_id = \"" . $user_id . "\"";
    $conn->query($sql);
}
function drawCard($userid, $conn)
{
    // reshuffle old cards
    $sql = "SELECT * FROM game_card WHERE play_status = 0 AND user_id = \"" . $userid . "\"";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        $sql = "SELECT * FROM game_card WHERE play_status = 1 AND user_id = \"" . $userid . "\"";
        $result = $conn->query($sql);
        if ($result->num_rows <= 5) {
            $sql = "UPDATE game_card SET play_status = 0 WHERE play_status = 2 AND user_id = \"" . $userid . "\"";
            $conn->query($sql);
        }
    }
    //draw new cards

    $sql = "UPDATE game_card SET play_status = 1 WHERE play_status = 0 AND  user_id = \"" . $userid . "\" ORDER BY RAND () LIMIT 1";
    $conn->query($sql);
}
function drawCardNoExceptions($userid, $conn)
{

    // reshuffle old cards
    $sql = "SELECT * FROM game_card WHERE play_status = 0 AND user_id = \"" . $userid . "\"";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        $sql = "UPDATE game_card SET play_status = 0 WHERE play_status = 2 AND user_id = \"" . $userid . "\"";
        $conn->query($sql);
    }
    //draw new cards

    $sql = "UPDATE game_card SET play_status = 1 WHERE play_status = 0 AND  user_id = \"" . $userid . "\" ORDER BY RAND () LIMIT 1";
    $conn->query($sql);
}
function getPreviousLogImage($conn)
{

    $sql = "SELECT log_icon FROM activity_log ORDER BY inc desc LIMIT 1";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        return $row["log_icon"];
    } else {
        return "Skip.png";
    }
}
function getPreviousAfterLogImage($conn)
{

    $sql = "SELECT log_icon FROM activity_log ORDER BY inc desc LIMIT 1 OFFSET 1";
    $result = $conn->query($sql);
    if ($row = $result->fetch_assoc()) {
        return $row["log_icon"];
    } else {
        return "Skip.png";
    }
}
function chess_ability($conn, $user_id)
{
    $pawnCount = get_pawn_count($conn, $user_id);
    echo $pawnCount;
    drawCard($user_id, $conn);
    while ($pawnCount != get_pawn_count($conn, $user_id)) {
        drawCard($user_id, $conn);
        $pawnCount = get_pawn_count($conn, $user_id);
        echo $pawnCount;
    }
}
function get_pawn_count($conn, $user_id)
{
    $sql = "SELECT * FROM game_card INNER JOIN deck_card ON game_card.card_id = deck_card.card_id WHERE play_status = 1 AND card_name = \"Pawn\" AND user_id = " . $user_id;
    $result = $conn->query($sql);
    return $result->num_rows;
}
//
//CODE
//
//
//
//
$servername = "localhost";
$username = "cardclash";
$password = "tjnlastjuniorproj";
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
$user_id;
$sql = "SELECT user.user_id FROM game_player INNER JOIN user on user.user_id = game_player.user_id WHERE username = \"" . $username . "\"";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Failed: Not oldest turn");
} elseif ($row = $result->fetch_assoc()) {
    $user_id = $row["user_id"];
} else {
    die("User ID not found");
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
//USSR deck: Nerf health
$prevLog = getPreviousLogImage($conn);
if($prevLog == "ussr/mass_assault.png" || getPreviousAfterLogImage($conn) == "ussr/mass_assault.png"){
    $health = 0;
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
    $sql = "INSERT INTO activity_log (log_msg, log_icon) VALUES (\"" . $msg . "\", \"" . $sprite . "\");";
    $result = $conn->query($sql);
} else {

    $sql = "INSERT INTO activity_log (log_msg, log_icon) VALUES (\"" . $username . " skipped their turn\", 'skip.png');";
    $result = $conn->query($sql);
}

//If playing aircraft or aircraft played last turn, draw a card and pass to the next player.
if (($cardid == 11 || $prevLog == "navy/carrier.png")) {
    //Do nothing. Turn already done. 
} else if ($cardid == 30) {
    chess_ability($conn, $user_id);
    incrementTurn($user_id, $conn);
} else if ($cardid == 40) {
    //USSR: Mass assault
    drawCard($user_id, $conn);
    drawCard($user_id, $conn);
} else if ($prevLog == "ussr/mass_assault.png") {
    //Do nothing
} else {
    incrementTurn($user_id, $conn);
    drawCard($user_id, $conn);
}

//Mobilization: Draw 2 cards
if ($cardid == 10) {
    drawCard($user_id, $conn);
    drawCard($user_id, $conn);
}

$out = "executed turn";
echo $out;
?>