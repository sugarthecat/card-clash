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

$id = $_GET["id"];
if (is_numeric($id) != 1) {
    die("{\"error\": \"ID must consist only of numbers: " .($id) . "\"}");
}
$sql = "SELECT card_name, card_sprite, damage, health FROM deck_card WHERE deck_id = ".$id;
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $cards = "";
    if ($row = $result->fetch_assoc()) {
        $cards = "{\"name\":\"" . $row["card_name"] . "\", \"icon\":\"" . $row["card_sprite"] . "\", \"health\":\"" . $row["health"] . "\", \"damage\":\"" . $row["damage"] . "\"}";
    }
    while ($row = $result->fetch_assoc()) {
        $cards = $cards .",{\"name\":\"" . $row["card_name"] . "\", \"icon\":\"" . $row["card_sprite"] . "\", \"health\":\"" . $row["health"] . "\", \"damage\":\"" . $row["damage"] . "\"}";
    }
    die("{\"success\": true, \"cards\": [" . $cards . "]}");
    // output data of each row
} else {
    die("{\"error\": \"You need to sign in to access this page\"}");
}
?>