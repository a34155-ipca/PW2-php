<?php
// ================== LIGAÇÃO À BASE DE DADOS ==================
$host = "localhost";
$user = "root";
$pass = "";
$db   = "pw2";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro na ligação: " . $conn->connect_error);
}

// ============================================================
// Processamento dos formulários
// ============================================================

// --- CURSOS ---
if (isset($_GET['del_curso'])) {
    $id = intval($_GET['del_curso']);
    $stmt = $conn->prepare("DELETE FROM cursos WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: plano_estudos.php");
    exit;
}

if (isset($_POST['add_curso'])) {
    $nome = trim($_POST['nome']);
    if ($nome != '') {
        $stmt = $conn->prepare("INSERT INTO cursos (NOME_CURSO) VALUES (?)");
        $stmt->bind_param("s", $nome);
        $stmt->execute();
    }
    header("Location: plano_estudos.php");
    exit;
}

if (isset($_POST['edit_curso'])) {
    $id = intval($_POST['id']);
    $nome = trim($_POST['nome']);
    $stmt = $conn->prepare("UPDATE cursos SET NOME_CURSO = ? WHERE ID = ?");
    $stmt->bind_param("si", $nome, $id);
    $stmt->execute();
    header("Location: plano_estudos.php");
    exit;
}

// --- DISCIPLINAS ---
if (isset($_GET['del_disciplina'])) {
    $id = intval($_GET['del_disciplina']);
    $stmt = $conn->prepare("DELETE FROM disciplinas WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: plano_estudos.php");
    exit;
}

if (isset($_POST['add_disciplina'])) {
    $nome = trim($_POST['nome']);
    if ($nome != '') {
        $stmt = $conn->prepare("INSERT INTO disciplinas (NOME_DISCIPLINA) VALUES (?)");
        $stmt->bind_param("s", $nome);
        $stmt->execute();
    }
    header("Location: plano_estudos.php");
    exit;
}

if (isset($_POST['edit_disciplina'])) {
    $id = intval($_POST['id']);
    $nome = trim($_POST['nome']);
    $stmt = $conn->prepare("UPDATE disciplinas SET NOME_DISCIPLINA = ? WHERE ID = ?");
    $stmt->bind_param("si", $nome, $id);
    $stmt->execute();
    header("Location: plano_estudos.php");
    exit;
}

// --- PLANO DE ESTUDOS ---
if (isset($_GET['del_plano_curso']) && isset($_GET['del_plano_disciplina'])) {
    $curso_id = intval($_GET['del_plano_curso']);
    $disciplina_id = intval($_GET['del_plano_disciplina']);
    $stmt = $conn->prepare("DELETE FROM plano_estudo WHERE CURSO = ? AND DISCILINA = ?");
    $stmt->bind_param("ii", $curso_id, $disciplina_id);
    $stmt->execute();
    header("Location: plano_estudos.php");
    exit;
}

if (isset($_POST['add_plano'])) {
    $curso_id = intval($_POST['curso_id']);
    $disciplina_id = intval($_POST['disciplina_id']);
    if ($curso_id > 0 && $disciplina_id > 0) {
        $check = $conn->prepare("SELECT * FROM plano_estudo WHERE CURSO = ? AND DISCILINA = ?");
        $check->bind_param("ii", $curso_id, $disciplina_id);
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows == 0) {
            $stmt = $conn->prepare("INSERT INTO plano_estudo (CURSO, DISCILINA) VALUES (?, ?)");
            $stmt->bind_param("ii", $curso_id, $disciplina_id);
            $stmt->execute();
        }
    }
    header("Location: plano_estudos.php");
    exit;
}

// ============================================================
// Consultas para exibir os dados
// ============================================================
$cursos = $conn->query("SELECT * FROM cursos ORDER BY NOME_CURSO");
$disciplinas = $conn->query("SELECT * FROM disciplinas ORDER BY NOME_DISCIPLINA");

$plano = $conn->query("
    SELECT p.CURSO AS curso_id, p.DISCILINA AS disciplina_id,
           c.NOME_CURSO AS nome_curso,
           d.NOME_DISCIPLINA AS nome_disciplina
    FROM plano_estudo p
    JOIN cursos c ON p.CURSO = c.ID
    JOIN disciplinas d ON p.DISCILINA = d.ID
    ORDER BY c.NOME_CURSO, d.NOME_DISCIPLINA
");
?>
<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<title>Gestão de Cursos, Disciplinas e Plano de Estudos</title>
<style>
body { font-family: Arial; margin: 20px; background: #f4f4f4; }
h1 { color: #333; }
.container { display: flex; gap: 20px; flex-wrap: wrap; }
.box { background: white; padding: 15px; border-radius: 5px; box-shadow: 0 0 10px #ccc; flex: 1 1 300px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 14px; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background: #f2f2f2; }
.actions a { margin-right: 5px; text-decoration: none; color: #007bff; }
.form-group { margin-bottom: 10px; }
.form-group label { display: block; font-weight: bold; }
.form-group input, .form-group select { width: 100%; padding: 5px; }
.btn { background: #007bff; color: white; padding: 5px 10px; border: none; cursor: pointer; }
</style>
</head>
<body>

<h1>Gestão de Cursos, Disciplinas e Plano de Estudos</h1>

<div class="container">

<!-- CURSOS -->
<div class="box">
<h2>Cursos</h2>
<form method="POST">
<label>Nome do Curso:</label>
<input type="text" name="nome" required>
<button type="submit" name="add_curso" class="btn">Adicionar</button>
</form>
<hr>
<table>
<tr><th>ID</th><th>Nome</th><th>Ações</th></tr>
<?php while ($row = $cursos->fetch_assoc()): ?>
<tr>
<td><?= $row['ID'] ?></td>
<td><?= htmlspecialchars($row['NOME_CURSO']) ?></td>
<td>
<a href="?edit_curso=<?= $row['ID'] ?>">Editar</a>
<a href="?del_curso=<?= $row['ID'] ?>">Excluir</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- DISCIPLINAS -->
<div class="box">
<h2>Disciplinas</h2>
<form method="POST">
<label>Nome da Disciplina:</label>
<input type="text" name="nome" required>
<button type="submit" name="add_disciplina" class="btn">Adicionar</button>
</form>
<hr>
<table>
<tr><th>ID</th><th>Nome</th><th>Ações</th></tr>
<?php while ($row = $disciplinas->fetch_assoc()): ?>
<tr>
<td><?= $row['ID'] ?></td>
<td><?= htmlspecialchars($row['NOME_DISCIPLINA']) ?></td>
<td>
<a href="?edit_disciplina=<?= $row['ID'] ?>">Editar</a>
<a href="?del_disciplina=<?= $row['ID'] ?>">Excluir</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- PLANO -->
<div class="box">
<h2>Plano de Estudos</h2>
<form method="POST">
<label>Curso:</label>
<select name="curso_id" required>
<option value="">Selecione</option>
<?php
$cursos_list = $conn->query("SELECT ID, NOME_CURSO FROM cursos ORDER BY NOME_CURSO");
while ($c = $cursos_list->fetch_assoc()):
?>
<option value="<?= $c['ID'] ?>"><?= $c['NOME_CURSO'] ?></option>
<?php endwhile; ?>
</select>

<label>Disciplina:</label>
<select name="disciplina_id" required>
<option value="">Selecione</option>
<?php
$disc_list = $conn->query("SELECT ID, NOME_DISCIPLINA FROM disciplinas ORDER BY NOME_DISCIPLINA");
while ($d = $disc_list->fetch_assoc()):
?>
<option value="<?= $d['ID'] ?>"><?= $d['NOME_DISCIPLINA'] ?></option>
<?php endwhile; ?>
</select>

<button type="submit" name="add_plano" class="btn">Vincular</button>
</form>

<hr>
<table>
<tr><th>Curso</th><th>Disciplina</th><th>Ações</th></tr>
<?php while ($row = $plano->fetch_assoc()): ?>
<tr>
<td><?= $row['nome_curso'] ?></td>
<td><?= $row['nome_disciplina'] ?></td>
<td>
<a href="?del_plano_curso=<?= $row['curso_id'] ?>&del_plano_disciplina=<?= $row['disciplina_id'] ?>">Excluir</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>
</body>
</html>