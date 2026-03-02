<?php
include('config.php');

// Buscar dados do utilizador logado, incluindo a foto
$stmt_user = $conn->prepare("SELECT foto FROM usuarios WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$dados_user = $stmt_user->get_result()->fetch_assoc();
$foto_perfil = $dados_user['foto'] ? $dados_user['foto'] : 'default.png';

// 1. Verificação de Segurança: Garante que o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['usuario_id']; 

// --- PROCESSAMENTO DE DADOS (Gravando com o ID do Usuário) ---

if (isset($_POST['add_curso'])) {
    $nome = trim($_POST['nome']);
    $stmt = $conn->prepare("INSERT INTO cursos (NOME_CURSO, SIGLA, USUARIO_ID) VALUES (?, 'N/A', ?)");
    $stmt->bind_param("si", $nome, $user_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['add_disciplina'])) {
    $nome = trim($_POST['nome']);
    $stmt = $conn->prepare("INSERT INTO disciplinas (NOME_DISCIPLINA, SIGLA, USUARIO_ID) VALUES (?, 'N/A', ?)");
    $stmt->bind_param("si", $nome, $user_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['vincular'])) {
    $c_id = $_POST['curso_id'];
    $d_id = $_POST['disc_id'];
    // Vincula apenas se ambos pertencerem ao usuário (Segurança extra)
    $stmt = $conn->prepare("INSERT IGNORE INTO plano_estudo (CURSO, DISCILINA, USUARIO_ID) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $c_id, $d_id, $user_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

if (isset($_GET['del_plano'])) {
    $stmt = $conn->prepare("DELETE FROM plano_estudo WHERE CURSO = ? AND DISCILINA = ? AND USUARIO_ID = ?");
    $stmt->bind_param("iii", $_GET['c'], $_GET['d'], $user_id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}

// --- CONSULTAS PARA EXIBIÇÃO (Filtradas por Usuário) ---

// Cursos para o Select
$stmt_c = $conn->prepare("SELECT * FROM cursos WHERE USUARIO_ID = ? ORDER BY NOME_CURSO");
$stmt_c->bind_param("i", $user_id);
$stmt_c->execute();
$res_cursos = $stmt_c->get_result();

// Disciplinas para o Select
$stmt_d = $conn->prepare("SELECT * FROM disciplinas WHERE USUARIO_ID = ? ORDER BY NOME_DISCIPLINA");
$stmt_d->bind_param("i", $user_id);
$stmt_d->execute();
$res_disciplinas = $stmt_d->get_result();

// Dados da Tabela Principal
$sql_plano = "SELECT c.NOME_CURSO, d.NOME_DISCIPLINA, p.CURSO, p.DISCILINA 
              FROM plano_estudo p 
              JOIN cursos c ON p.CURSO = c.ID 
              JOIN disciplinas d ON p.DISCILINA = d.ID
              WHERE p.USUARIO_ID = ? 
              ORDER BY c.NOME_CURSO";
$stmt_p = $conn->prepare($sql_plano);
$stmt_p->bind_param("i", $user_id);
$stmt_p->execute();
$plano_result = $stmt_p->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Académico | Modern Design</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --glass-bg: rgba(255, 255, 255, 0.9); --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        body { font-family: 'Inter', sans-serif; background-color: #f0f2f5; color: #334155; }
        .navbar { background: var(--primary-gradient) !important; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .card { border: none; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.02); background: var(--glass-bg); backdrop-filter: blur(10px); }
        .card-header { background: transparent; border-bottom: 1px solid #f1f5f9; font-weight: 700; color: #1e293b; padding: 1.25rem; }
        .form-control, .form-select { border-radius: 10px; padding: 0.6rem 1rem; border: 1px solid #e2e8f0; }
        .btn-primary { background: var(--primary-gradient); border: none; border-radius: 10px; padding: 0.6rem 1.2rem; font-weight: 600; }
        .table { border-collapse: separate; border-spacing: 0 8px; }
        .table tr { background: white; border-radius: 10px; }
        .table td, .table th { border: none; padding: 1rem; vertical-align: middle; }
        .badge-curso { background: #e0e7ff; color: #4338ca; font-weight: 600; padding: 0.5rem 1rem; border-radius: 8px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark py-3 mb-5">
    <div class="ms-auto d-flex align-items-center">
    <div class="text-white me-3 d-flex align-items-center">
        <img src="uploads/<?= $_SESSION['foto'] ?>" 
             class="rounded-circle me-2 border border-2 border-white" 
             style="width: 40px; height: 40px; object-fit: cover;">
        
        <div class="d-none d-sm-block">
            <small class="opacity-75">Bem-vindo,</small>
            <div class="fw-bold"><?= htmlspecialchars($_SESSION['username']) ?></div>
        </div>
    </div>
    <a href="logout.php" class="btn btn-light btn-sm fw-bold px-3">Sair</a>
</div>
</nav>

<div class="container">
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-plus me-2 text-primary"></i>Novo Curso</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="input-group">
                            <input type="text" name="nome" class="form-control" placeholder="Nome do Curso" required>
                            <button name="add_curso" class="btn btn-primary">Criar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-book-open me-2 text-success"></i>Nova Disciplina</div>
                <div class="card-body">
                    <form method="POST">
                        <div class="input-group">
                            <input type="text" name="nome" class="form-control" placeholder="Nome da Disciplina" required>
                            <button name="add_disciplina" class="btn btn-primary">Criar</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-lg" style="background: #1e293b; color: white;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-link me-2"></i>Vincular Plano</h5>
                    <form method="POST">
                        <div class="mb-3">
                            <select name="curso_id" class="form-select bg-dark text-white border-secondary" required>
                                <option value="">Selecionar Curso</option>
                                <?php while($c = $res_cursos->fetch_assoc()): ?>
                                    <option value="<?= $c['ID'] ?>"><?= htmlspecialchars($c['NOME_CURSO']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select name="disc_id" class="form-select bg-dark text-white border-secondary" required>
                                <option value="">Selecionar Disciplina</option>
                                <?php while($d = $res_disciplinas->fetch_assoc()): ?>
                                    <option value="<?= $d['ID'] ?>"><?= htmlspecialchars($d['NOME_DISCIPLINA']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button name="vincular" class="btn btn-primary w-100 py-2">Confirmar Vínculo</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list me-2"></i>Plano de Estudos de <?= htmlspecialchars($_SESSION['username']) ?></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr><th>Curso</th><th>Disciplina</th><th class="text-end">Gerir</th></tr>
                            </thead>
                            <tbody>
                                <?php if($plano_result->num_rows > 0): 
                                    while($row = $plano_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><span class="badge-curso"><?= htmlspecialchars($row['NOME_CURSO']) ?></span></td>
                                        <td class="fw-semibold text-secondary"><?= htmlspecialchars($row['NOME_DISCIPLINA']) ?></td>
                                        <td class="text-end">
                                            <a href="?del_plano=1&c=<?= $row['CURSO'] ?>&d=<?= $row['DISCILINA'] ?>" 
                                               class="btn btn-sm btn-outline-danger border-0 rounded-circle" 
                                               onclick="return confirm('Apagar este vínculo?')">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="3" class="text-center py-5 text-muted">Ainda não tens nada no teu plano.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>