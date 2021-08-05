<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rcms_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * from users";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "id: " . $row["StudentID"]. " - Name: " . $row["StudentName"]. " " . $row["CourseID"]. "<br>";
  }
} else {
  echo "0 results";
}
$conn->close();
?> 