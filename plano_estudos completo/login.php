<?php
include('config.php');

// Se já estiver logado, vai direto para o dashboard
if (isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['senha'];

    $stmt = $conn->prepare("SELECT id, username, senha FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($pass, $row['senha'])) {
            // Senha correta! Criar a sessão
            $_SESSION['usuario_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['foto'] = $row['foto']; 
            header("Location: dashboard.php");
            exit;
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Utilizador não encontrado.";
    }
}

?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login - Plano de Estudos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4 card p-4 shadow border-0">
                <h3 class="text-center mb-4">Entrar</h3>
                <?php if(isset($erro)) echo "<div class='alert alert-danger'>$erro</div>"; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Seu usuário" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha</label>
                        <input type="password" name="senha" class="form-control" placeholder="Sua senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    <div class="text-center mt-3">
                        Não tem conta? <a href="cadastro.php">Registe-se aqui</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>