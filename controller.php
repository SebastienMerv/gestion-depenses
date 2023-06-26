<?php

// Créer une conexion
$pdo = new PDO('mysql:host=localhost;dbname=depenses', 'root', 'root');
$pdo->exec("SET  NAMES UTF8");

if (
  isset($_POST['identifiant']) && isset($_POST['password']) 
) {


  $password = $_POST['password'];


  $sql = "INSERT INTO utilisateurs(`nom`,`password`) VALUES (?, ?)";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([$_POST["identifiant"], $password]);
  header("Location: index.php");
}
 


?>

