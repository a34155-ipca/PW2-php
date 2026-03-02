<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>DISCIPLINAS</h1>
    

    <?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pw2";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";
echo "<br>";





$sql = "SELECT * FROM disciplinas";
// Execute the SQL query
$result = $conn->query($sql);
 
// Process the result set
if ($result->num_rows > 0) {
  // Output data of each row
  while($row = $result->fetch_assoc()) {
    echo "id: " . $row["ID"]. " - Disciplina: " . $row["NOME_DISCIPLINA"]. " Sigla: " . $row["SIGLA"]. "<br>";
  }
} else {
  echo "0 results";
}
 
$conn->close();
 
?>

</body>
</html>