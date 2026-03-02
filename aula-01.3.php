<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Formulário</title>
</head>
<body>

<h2>Formulário de Contacto</h2>

<form method="POST" action="">
    Nome:<br>
    <input type="text" name="nome" required><br><br>

    Morada:<br>
    <input type="text" name="morada" required><br><br>

    Email:<br>
    <input type="email" name="email" required><br><br>

    Telefone:<br>
    <input type="text" name="telefone" required><br><br>

    Senha:<br>
    <input type="password" name="senha" required><br><br>

    <input type="submit" name="enviar" value="Guardar">
    
</form>
<?php

if(isset($_POST['enviar'])) {

    $nome = $_POST['nome'];
    $morada = $_POST['morada'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $senha = md5($_POST['senha']);

    // Formatar dados
    $dados = "Nome: $nome | Morada: $morada | Email: $email | Telefone: $telefone | Senha: $senha" . PHP_EOL;

    // Guardar no ficheiro (cria se não existir)
    file_put_contents("dados.txt", $dados, FILE_APPEND);

    echo "<p style='color:green;'>Dados guardados com sucesso!</p>";
}

?>
</body>
</html>
