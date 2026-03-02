<?php
session_start();
$_SESSION = array(); // Limpa todas as variáveis
session_destroy();
header("Location: login.php");
exit;
?>