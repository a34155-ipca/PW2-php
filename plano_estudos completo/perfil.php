<?php
include('config.php');
if (!isset($_SESSION['usuario_id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['usuario_id'];

if (isset($_POST['subir_foto'])) {
    $arquivo = $_FILES['foto'];
    
    // Validar extensão
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $novo_nome = md5(time()) . "." . $extensao; // Nome aleatório para não repetir
    $diretorio = "uploads/";

    if (move_uploaded_file($arquivo['tmp_name'], $diretorio . $novo_nome)) {
        $stmt = $conn->prepare("UPDATE usuarios SET foto = ? WHERE id = ?");
        $stmt->bind_param("si", $novo_nome, $user_id);
        $stmt->execute();
        header("Location: dashboard.php");
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Upload de Foto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 mx-auto" style="max-width: 400px;">
            <h4>Alterar Foto de Perfil</h4>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" name="foto" class="form-control mb-3" required>
                <button name="subir_foto" class="btn btn-primary w-100">Salvar Foto</button>
            </form>
            <a href="dashboard.php" class="btn btn-link w-100 mt-2">Voltar</a>
        </div>
    </div>
</body>
</html>