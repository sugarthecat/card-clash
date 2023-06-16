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
$sql = "SELECT * FROM user WHERE LOWER(username) = LOWER(\"" . $username . "\") AND password = \"" . $password . "\"";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    die("{\"success\": true, \"username\": \"" . $username . "\"}");
    // output data of each row
} else {
    die("{\"error\": \"Invalid login\"}");
}
?>