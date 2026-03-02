<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "pw2";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na ligação: " . $conn->connect_error);
}

// Função para verificar se o utilizador está logado
function verificarLogado() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit;
    }
}
?>