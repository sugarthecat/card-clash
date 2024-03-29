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
if ($username != preg_replace("/[^a-zA-Z0-9]+/", "", $username)) {
    die("{\"error\": \"Username must consist only of letters and numbers\"}");
}
if ($password != preg_replace("/[^a-zA-Z0-9]+/", "", $password)) {
    die("{\"error\": \"password must consist only of letters and numbers\"}");
}
$sql = "SELECT deck_name, folder, deck.deck_id, user.selected_deck = deck.deck_id AS selected FROM deck INNER JOIN deck_ownership ON deck.deck_id = deck_ownership.deck_id INNER JOIN user on deck_ownership.user_id = user.user_id WHERE LOWER(username) = LOWER(\"" . $username . "\") AND password = \"" . $password . "\"";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $cards = "";
    if ($row = $result->fetch_assoc()) {
        $cards = "{\"name\":\"" . $row["deck_name"] . "\", \"icon\":\"" . $row["folder"] . "/icon.png\", \"id\": \"" . $row["deck_id"] . "\", \"selected\": \"" . $row["selected"] . "\"}";
    }
    while ($row = $result->fetch_assoc()) {
        $cards = $cards .",{\"name\":\"" . $row["deck_name"] . "\", \"icon\":\"" . $row["folder"] . "/icon.png\", \"id\": \"" . $row["deck_id"] . "\", \"selected\": \"" . $row["selected"] . "\"}";
    }
    die("{\"success\": true, \"decks\": [" . $cards . "]}");
    // output data of each row
} else {
    die("{\"error\": \"You need to sign in to access this page\"}");
}
?>