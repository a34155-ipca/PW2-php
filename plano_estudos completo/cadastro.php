<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = trim($_POST['username']);
    $pass = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    
    // Caminho da pasta
    $diretorio = 'uploads/';
    $foto_nome = "default.png";

    // 1. Verificar se o usuário já existe antes de tentar inserir
    $check = $conn->prepare("SELECT id FROM usuarios WHERE username = ?");
    $check->bind_param("s", $user);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $erro = "Este nome de utilizador já está em uso!";
    } else {
        // 2. Processar Foto apenas se não houver erro de nome
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                $novo_nome = md5(time() . $user) . '.' . $ext;
                // move_uploaded_file precisa da pasta já criada manualmente
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $diretorio . $novo_nome)) {
                    $foto_nome = $novo_nome;
                }
            }
        }

        // 3. Inserir no banco
        $stmt = $conn->prepare("INSERT INTO usuarios (username, senha, foto) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user, $pass, $foto_nome);
        
        if ($stmt->execute()) {
            $sucesso = "Conta criada! <a href='login.php'>Faça login agora</a>";
        } else {
            $erro = "Erro crítico ao salvar no banco de dados.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Registo | EduSmart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #667eea; display: flex; align-items: center; min-height: 100vh; }
        .card { border-radius: 15px; width: 100%; max-width: 400px; margin: auto; }
    </style>
</head>
<body>
    <div class="card p-4 shadow">
        <h3 class="text-center mb-4">Criar Conta</h3>
        <?php if(isset($sucesso)) echo "<div class='alert alert-success'>$sucesso</div>"; ?>
        <?php if(isset($erro)) echo "<div class='alert alert-danger'>$erro</div>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto de Perfil</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary w-100">Registar</button>
            <p class="text-center mt-3"><a href="login.php">Já tenho conta</a></p>
        </form>
    </div>
</body>
</html>