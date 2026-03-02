<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Teste</h1>
    <?php 
    if($_SERVER["REQUEST_METHOD"] == "POST"){
       $username = htmlspecialchars(string:$_POST["user"]);
       $password = $_POST["pass"];
       if($username == "admin" && $password == "12345"){
      session_start();
      $_SESSION["status"] = "logged_in";
      echo "Bem-vindo, $username!";
       } else {
        echo "Credenciais inválidas. Tente novamente.";
       }
    }


?>
    <form method="post" action="<?php echo htmlspecialchars(string:$_SERVER["PHP_SELF"]); ?>">
        <label for="user">Usuário:</label>
        <input type="text" id="user" name="user" required>
        <br>
        <label for="pass">Senha:</label>
        <input type="password" id="pass" name="pass" required>
        <br>
        <input type="submit" value="Entrar">
    </form>
</body>
</html>
